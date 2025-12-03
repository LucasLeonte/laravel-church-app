<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixChapterFiles extends Command
{
    protected $signature = 'bible:fix-files {--translation=WEB : Translation folder name}';
    protected $description = 'Repair chapter JSON files that contain two JSON objects (meta then verses) into a single JSON object';

    public function handle()
    {
        $translation = $this->option('translation') ?: 'WEB';
        $dir = storage_path('app/bible/' . $translation);
        if (!is_dir($dir)) {
            $this->error("Directory not found: {$dir}");
            return 1;
        }

        $files = scandir($dir);
        $fixed = 0;
        foreach ($files as $f) {
            $path = $dir . DIRECTORY_SEPARATOR . $f;
            if (!is_file($path) || substr($f, -5) !== '.json') continue;

            $content = file_get_contents($path);
            if (trim($content) === '') continue;

            // Detect if file contains two JSON objects adjacent (starts with {..}\n{..})
            $trim = ltrim($content);
            if (strpos($trim, '{') !== 0) continue;

            // Try to split by newline between objects
            $parts = preg_split('/\s*}\s*\n\s*\{/', $content, 2);
            if (count($parts) !== 2) {
                // maybe the file is already a single object or uses other spacing; try to json_decode whole
                $decoded = json_decode($content, true);
                if (is_array($decoded) && array_key_exists('verses', $decoded)) {
                    continue; // already good
                }
                // else try to find first object end by scanning
                // fallback: continue
                continue;
            }

            // Reconstruct two JSON strings
            $json1 = rtrim($parts[0]) . '}';
            $json2 = '{' . ltrim($parts[1]);

            $d1 = json_decode($json1, true);
            $d2 = json_decode($json2, true);

            if (!is_array($d1) || !is_array($d2)) {
                $this->warn("Skipping file (invalid json parts): {$f}");
                continue;
            }

            // Merge into single structure. Ensure verses present as array
            $out = $d1;
            if (isset($d2['verses'])) {
                $out['verses'] = $d2['verses'];
            } else {
                // merge other keys
                $out = array_merge($out, $d2);
            }

            // Write back pretty JSON
            file_put_contents($path, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $fixed++;
        }

        $this->info("Fixed {$fixed} files under {$dir}");
        return 0;
    }
}

