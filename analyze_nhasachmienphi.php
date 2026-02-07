<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

$bookUrl = 'https://nhasachmienphi.com/diep-vien-007-song-bac-hoang-gia.html';

echo "Fetching: $bookUrl\n";
echo "==================================\n\n";

try {
    $response = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ])->timeout(30)->get($bookUrl);

    if ($response->successful()) {
        $html = $response->body();

        echo "✓ Page fetched successfully!\n";
        echo "HTML length: " . strlen($html) . " bytes\n\n";

        // Save HTML to file for analysis
        file_put_contents('nhasachmienphi_sample.html', $html);
        echo "✓ Saved HTML to nhasachmienphi_sample.html\n\n";

        // Find all chapter links
        echo "Looking for chapter links...\n";
        echo "==================================\n\n";

        // Pattern 1: Look for links in list items
        if (preg_match_all('/<li[^>]*>\s*<a[^>]*href="([^"]+)"[^>]*>([^<]+)<\/a>\s*<\/li>/is', $html, $matches, PREG_SET_ORDER)) {
            echo "Found " . count($matches) . " links in <li> tags:\n";
            foreach (array_slice($matches, 0, 10) as $i => $match) {
                echo "  " . ($i + 1) . ". URL: " . $match[1] . "\n";
                echo "     Text: " . trim($match[2]) . "\n";
            }
            echo "\n";
        }

        // Pattern 2: Look for specific chapter patterns
        if (preg_match_all('/<a[^>]*href="([^"]*chuong[^"]*)"[^>]*>([^<]*)<\/a>/i', $html, $matches, PREG_SET_ORDER)) {
            echo "Found " . count($matches) . " links with 'chuong' in URL:\n";
            foreach (array_slice($matches, 0, 10) as $i => $match) {
                echo "  " . ($i + 1) . ". URL: " . $match[1] . "\n";
                echo "     Text: " . trim($match[2]) . "\n";
            }
            echo "\n";
        }

        // Pattern 3: Look for table with chapters
        if (preg_match('/<table[^>]*class="[^"]*table[^"]*"[^>]*>(.*?)<\/table>/is', $html, $tableMatch)) {
            echo "Found table. Extracting rows...\n";
            if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $tableMatch[1], $rowMatches, PREG_SET_ORDER)) {
                echo "Found " . count($rowMatches) . " table rows:\n";
                foreach (array_slice($rowMatches, 0, 10) as $i => $row) {
                    if (preg_match('/<a[^>]*href="([^"]+)"[^>]*>([^<]*)<\/a>/i', $row[1], $linkMatch)) {
                        echo "  " . ($i + 1) . ". URL: " . $linkMatch[1] . "\n";
                        echo "     Text: " . trim($linkMatch[2]) . "\n";
                    }
                }
            }
            echo "\n";
        }

        // Pattern 4: Look for div with muc-luc or contents
        if (preg_match('/<div[^>]*(?:class|id)="[^"]*(?:muc|luc|contents|chapters)[^"]*"[^>]*>(.*?)<\/div>/is', $html, $tocMatch)) {
            echo "Found TOC div. Content preview:\n";
            echo substr($tocMatch[1], 0, 500) . "...\n\n";
        }

        // Pattern 5: Find all links with "chuong" or chapter text
        if (preg_match_all('/<a[^>]*href="([^"]+)"[^>]*>[^<]*(?:Chương|Chapter|chuong)[^<]*<\/a>/i', $html, $matches, PREG_SET_ORDER)) {
            echo "Found " . count($matches) . " chapter links:\n";
            foreach (array_slice($matches, 0, 10) as $i => $match) {
                echo "  " . ($i + 1) . ". " . $match[1] . "\n";
            }
        }

        // Show HTML structure around "Chương" or chapter keywords
        echo "\n\nSearching for 'Chương' in HTML...\n";
        echo "==================================\n";

        // Find position of first "Chương" mention
        $pos = stripos($html, 'Chương');
        if ($pos !== false) {
            echo "Found 'Chương' at position $pos\n";
            echo "Context (500 chars before and after):\n";
            $start = max(0, $pos - 500);
            echo substr($html, $start, 1500) . "\n";
        }
    } else {
        echo "✗ Failed to fetch page. Status: " . $response->status() . "\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
