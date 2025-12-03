<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SplitBibleJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bible:split {--input= : Path to the input JSON (relative to storage/app/bible or absolute)} {--translation=WEB : Translation code/folder name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stream and split a large Bible JSON (single file of verses) into per-chapter JSON files under storage/app/bible/{TRANSLATION}';

    public function handle()
    {
        $input = $this->option('input') ?: storage_path('app/bible/web.json');
        if (!\file_exists($input)) {
            // allow relative to storage/app/bible
            $alt = storage_path('app/bible/' . ltrim($this->option('input'), '\\/'));
            if ($this->option('input') && file_exists($alt)) {
                $input = $alt;
            } else {
                $this->error("Input file not found: {$input}");
                return 1;
            }
        }

        $translation = $this->option('translation') ?: 'WEB';
        $outDir = storage_path('app/bible/' . $translation);
        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $this->info("Reading input: {$input}");
        $this->info("Output directory: {$outDir}");

        $handle = fopen($input, 'r');
        if (!$handle) {
            $this->error('Failed to open input file.');
            return 1;
        }

        // Find the "verses" array start. We'll search incrementally for the token "\"verses\"" and the following '['
        $buffer = '';
        $found = false;
        while (!feof($handle) && !$found) {
            $chunk = fread($handle, 8192);
            if ($chunk === false) break;
            $buffer .= $chunk;
            if (strpos($buffer, '"verses"') !== false) {
                $pos = strpos($buffer, '[' , strpos($buffer, '"verses"'));
                if ($pos !== false) {
                    // Move file pointer to the position after this '[' relative to file start
                    $absolutePos = ftell($handle) - strlen($buffer) + $pos + 1; // +1 to put after '['
                    fseek($handle, $absolutePos);
                    $found = true;
                    break;
                }
            }
            // keep last few chars to handle token across chunk boundaries
            if (strlen($buffer) > 1000) {
                $buffer = substr($buffer, -1000);
            }
        }

        if (!$found) {
            // as fallback, try to find first '[' in file
            rewind($handle);
            $all = fread($handle, 1000000);
            $pos = strpos($all, '[');
            if ($pos === false) {
                $this->error('Could not locate JSON array start in the file.');
                fclose($handle);
                return 1;
            }
            fseek($handle, $pos + 1);
        }

        $this->info('Starting object streaming parse...');

        $depth = 0;
        $inString = false;
        $escape = false;
        $objBuf = '';
        $files = []; // map chapterKey => [handle, firstFlag, bookName, chapter]
        $count = 0;

        while (!feof($handle)) {
            $chunk = fread($handle, 8192);
            if ($chunk === false || $chunk === '') break;
            $len = strlen($chunk);
            for ($i = 0; $i < $len; $i++) {
                $c = $chunk[$i];
                // handle string state
                if ($inString) {
                    $objBuf .= $c;
                    if ($escape) {
                        $escape = false;
                    } elseif ($c === '\\') {
                        $escape = true;
                    } elseif ($c === '"') {
                        $inString = false;
                    }
                    continue;
                }

                if ($c === '"') {
                    $inString = true;
                    $objBuf .= $c;
                    continue;
                }

                if ($c === '{') {
                    $depth++;
                    $objBuf .= $c;
                    continue;
                }

                if ($c === '}') {
                    $objBuf .= $c;
                    $depth--;
                    if ($depth === 0) {
                        // complete object in $objBuf
                        $raw = trim($objBuf);
                        // strip potential trailing commas
                        $raw = rtrim($raw, ",\n\r \t");

                        try {
                            $data = json_decode($raw, true);
                        } catch (\Throwable $e) {
                            $this->error('JSON decode error: ' . $e->getMessage());
                            fclose($handle);
                            return 1;
                        }

                        if (is_array($data)) {
                            // Expect fields: book_name/book/chapter/verse/text
                            $bookName = $data['book_name'] ?? ($data['book'] ?? 'Unknown');
                            $chapter = $data['chapter'] ?? 0;
                            $verse = $data['verse'] ?? null;
                            $text = $data['text'] ?? null;

                            // normalize book name for filename
                            $bookFile = preg_replace('/[^A-Za-z0-9_\-]/', '_', $bookName);
                            $chapterKey = $bookFile . '_' . intval($chapter);
                            $outPath = $outDir . DIRECTORY_SEPARATOR . $bookFile . '_' . intval($chapter) . '.json';

                            if (!isset($files[$chapterKey])) {
                                // open and write prefix
                                $fh = fopen($outPath, 'w');
                                if (!$fh) {
                                    $this->error("Failed to open output file: {$outPath}");
                                    continue;
                                }
                                $meta = [
                                    'translation' => $translation,
                                    'book' => $bookName,
                                    'chapter' => intval($chapter),
                                ];
                                fwrite($fh, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                                fwrite($fh, "\n");
                                fwrite($fh, '{"verses":[');
                                $files[$chapterKey] = [
                                    'handle' => $fh,
                                    'first' => true,
                                    'path' => $outPath,
                                ];
                            }

                            // append verse object (keep verse number and text)
                            $verseObj = ['verse' => $verse, 'text' => $text];
                            $fh = $files[$chapterKey]['handle'];
                            if ($files[$chapterKey]['first']) {
                                fwrite($fh, json_encode($verseObj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                                $files[$chapterKey]['first'] = false;
                            } else {
                                fwrite($fh, ',' . json_encode($verseObj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                            }

                            $count++;
                        }

                        $objBuf = '';
                        continue;
                    }
                    continue;
                }

                if ($depth > 0) {
                    $objBuf .= $c;
                }

                // stop early if we reached end of array ']' maybe break
                if ($c === ']') {
                    // end of the verses array
                    break 2;
                }
            }
        }

        // close files and write suffix
        foreach ($files as $k => $info) {
            $fh = $info['handle'];
            fwrite($fh, '] }');
            fclose($fh);
        }

        fclose($handle);

        $this->info("Processed {$count} verse objects. Created " . count($files) . " chapter files.");
        return 0;
    }
}

