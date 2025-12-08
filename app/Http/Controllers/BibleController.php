<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Services\BibleService;

class BibleController extends Controller
{
    // Return JSON for a single chapter
    public function chapter($translation, $book, $chapter): JsonResponse
    {
        $translation = strtoupper($translation);
        $book = str_replace('%20', ' ', $book);
        // Normalize book: remove undesirable chars, keep underscores for file names
        $bookFile = preg_replace('/[^A-Za-z0-9_\-]/', '_', $book);
        $chapter = intval($chapter);

        $cacheKey = "bible:$translation:$bookFile:$chapter";

        $data = Cache::remember($cacheKey, now()->addDays(30), function () use ($translation, $bookFile, $chapter) {
            $path = storage_path("app/bible/$translation/{$bookFile}_$chapter.json");
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
    public function index($translation = 'WEB'): JsonResponse
    {
        $translation = strtoupper($translation);
        $dir = storage_path("app/bible/$translation");
        if (!is_dir($dir)) {
            return Response::json(['message' => 'Index not found'], 404);
        }

        // build index array from files (delegated to service)
        $index = BibleService::buildIndexFromFolder($translation);

        // reorder index to canonical using the service
        $final = BibleService::reorderIndexToCanonical($index);

        $headers = [
            'Cache-Control' => 'no-store, max-age=0',
            'X-Bible-Index' => (string)time(),
        ];

        return Response::json($final, 200, $headers);
    }
}
