<?php

namespace App\Services;

class BibleService
{
    /**
     * Canonical Protestant Bible order.
     * @return string[]
     */
    public static function canonical(): array
    {
        return [
            'Genesis','Exodus','Leviticus','Numbers','Deuteronomy','Joshua','Judges','Ruth','1 Samuel','2 Samuel','1 Kings','2 Kings','1 Chronicles','2 Chronicles','Ezra','Nehemiah','Esther','Job','Psalms','Proverbs','Ecclesiastes','Song of Solomon','Isaiah','Jeremiah','Lamentations','Ezekiel','Daniel','Hosea','Joel','Amos','Obadiah','Jonah','Micah','Nahum','Habakkuk','Zephaniah','Haggai','Zechariah','Malachi','Matthew','Mark','Luke','John','Acts','Romans','1 Corinthians','2 Corinthians','Galatians','Ephesians','Philippians','Colossians','1 Thessalonians','2 Thessalonians','1 Timothy','2 Timothy','Titus','Philemon','Hebrews','James','1 Peter','2 Peter','1 John','2 John','3 John','Jude','Revelation'
        ];
    }

    /**
     * Normalize a book name for consistent matching.
     */
    public static function normalize(string $name): string
    {
        $n = mb_strtolower(trim($name));
        $n = preg_replace('/[^a-z0-9]+/u', ' ', $n);
        return preg_replace('/\s+/', ' ', $n);
    }

    /**
     * Scan storage/app/bible/{translation} and build an index array.
     * Returns array of ['book' => string, 'chapters' => array, 'chapter_count' => int]
     */
    public static function buildIndexFromFolder(string $translation): array
    {
        $dir = storage_path("app/bible/{$translation}");
        if (!is_dir($dir)) {
            return [];
        }

        $files = scandir($dir);
        $books = [];
        foreach ($files as $f) {
            if (!is_file($dir . DIRECTORY_SEPARATOR . $f)) continue;
            if (substr($f, -5) !== '.json') continue;
            $name = substr($f, 0, -5);
            $parts = explode('_', $name);
            if (count($parts) < 2) continue;
            $chapter = array_pop($parts);
            $bookFile = implode('_', $parts);
            $bookDisplay = str_replace('_', ' ', $bookFile);
            if (!isset($books[$bookDisplay])) $books[$bookDisplay] = [];
            $books[$bookDisplay][] = intval($chapter);
        }

        $index = [];
        foreach ($books as $book => $chapters) {
            sort($chapters, SORT_NUMERIC);
            $index[] = [
                'book' => $book,
                'chapters' => $chapters,
                'chapter_count' => count($chapters),
            ];
        }

        return $index;
    }
}

