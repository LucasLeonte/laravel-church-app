<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Services\BibleService;

class BibleController extends Controller
{
    // Return JSON for a single chapter
    public function chapter(Request $request, $translation, $book, $chapter): \Illuminate\Http\JsonResponse
    {
        $translation = strtoupper($translation);
        $book = str_replace('%20', ' ', $book);
        // Normalize book: remove undesirable chars, keep underscores for file names
        $bookFile = preg_replace('/[^A-Za-z0-9_\-]/', '_', $book);
        $chapter = intval($chapter);

        $cacheKey = "bible:{$translation}:{$bookFile}:{$chapter}";

        $data = Cache::remember($cacheKey, now()->addDays(30), function () use ($translation, $bookFile, $chapter) {
            $path = storage_path("app/bible/{$translation}/{$bookFile}_{$chapter}.json");
            if (!file_exists($path)) return null;
            $json = file_get_contents($path);
            if ($json === false) return null;
            return json_decode($json, true);
        });

        if (empty($data)) {
            return Response::json(['message' => 'Not found'], 404);
        }

        return Response::json($data, 200, [
            'Cache-Control' => 'public, max-age=' . (60*60*24),
        ]);
    }

    // Build and return the books index at runtime (ensures canonical sorting)
    public function index(Request $request, $translation = 'WEB'): \Illuminate\Http\JsonResponse
    {
        $translation = strtoupper($translation);
        $dir = storage_path("app/bible/{$translation}");
        if (!is_dir($dir)) {
            return Response::json(['message' => 'Index not found'], 404);
        }

        // build index array from files (delegated to service)
        $index = BibleService::buildIndexFromFolder($translation);

        // Build normalized map of index entries for quick lookup
        $normalizedIndex = [];
        foreach ($index as $item) {
            $normalizedIndex[BibleService::normalize($item['book'])] = $item;
        }

        $ordered = [];
        $usedKeys = [];

        foreach (BibleService::canonical() as $c) {
            $cn = BibleService::normalize($c);
            if (isset($normalizedIndex[$cn])) {
                $ordered[] = $normalizedIndex[$cn];
                $usedKeys[] = $cn;
                continue;
            }
            $foundKey = null;
            foreach ($normalizedIndex as $k => $v) {
                if (in_array($k, $usedKeys, true)) continue;
                if (str_contains($k, $cn) || str_contains($cn, $k)) {
                    $foundKey = $k;
                    break;
                }
            }
            if ($foundKey !== null) {
                $ordered[] = $normalizedIndex[$foundKey];
                $usedKeys[] = $foundKey;
            }
        }

        $remaining = [];
        foreach ($index as $item) {
            $key = BibleService::normalize($item['book']);
            if (in_array($key, $usedKeys, true)) continue;
            $remaining[] = $item;
        }
        usort($remaining, function ($a, $b) {
            return strcasecmp($a['book'], $b['book']);
        });

        $final = array_merge($ordered, $remaining);

        $headers = [
            'Cache-Control' => 'no-store, max-age=0',
            'X-Bible-Index' => (string)time(),
        ];

        return Response::json($final, 200, $headers);
    }
}
