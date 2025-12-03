<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BibleService;

class BuildBibleIndex extends Command
{
    protected $signature = 'bible:index {--translation=WEB : Translation folder name}';
    protected $description = 'Build a books index (book name and chapter count) from per-chapter JSON files under storage/app/bible/{translation}';

    public function handle()
    {
        $translation = $this->option('translation') ?: 'WEB';
        $dir = storage_path('app/bible/' . $translation);
        if (!is_dir($dir)) {
            $this->error("Directory not found: {$dir}");
            return 1;
        }

        // Use service to build index
        $index = BibleService::buildIndexFromFolder($translation);

        // Build normalized map of index entries for quick lookup
        $normalizedIndex = [];
        foreach ($index as $item) {
            $normalizedIndex[BibleService::normalize($item['book'])] = $item;
        }

        $ordered = [];
        $usedKeys = [];

        // First, take items that match canonical order (exact or substring match)
        foreach (BibleService::canonical() as $c) {
            $cn = BibleService::normalize($c);
            // exact match
            if (isset($normalizedIndex[$cn])) {
                $ordered[] = $normalizedIndex[$cn];
                $usedKeys[] = $cn;
                continue;
            }
            // substring match: find any normalized index key that contains $cn or vice versa
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

        // Append remaining books sorted alphabetically by display name
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

        $outPath = $dir . DIRECTORY_SEPARATOR . 'books_index.json';
        file_put_contents($outPath, json_encode($final, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info('Wrote index to: ' . $outPath . ' (' . count($final) . ' books)');
        return 0;
    }
}
