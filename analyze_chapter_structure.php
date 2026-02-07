<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

$bookUrl = 'https://nhasachmienphi.com/diep-vien-007-song-bac-hoang-gia.html';

echo "Phân tích cấu trúc nhasachmienphi.com\n";
echo "=====================================\n\n";

try {
    $response = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ])->timeout(30)->get($bookUrl);

    if (!$response->successful()) {
        throw new Exception("Failed to fetch page");
    }

    $html = $response->body();
    echo "✓ Fetched book page\n\n";

    // STEP 1: Extract chapters from div.box_chhr
    echo "STEP 1: Extracting chapter list from div.box_chhr\n";
    echo "==================================================\n\n";

    $chapters = [];

    // Pattern: div class='item_ch' containing <a> with chapter URL
    // <div class='item_ch'><a target="_blank" href='https://nhasachmienphi.com/doc-online/diep-vien-007-song-bac-hoang-gia-212667'>Chương 1</a></div>

    if (preg_match_all("/<div class='item_ch'><a[^>]*href='([^']+)'[^>]*>([^<]+)<\/a><\/div>/i", $html, $matches, PREG_SET_ORDER)) {
        echo "Found " . count($matches) . " chapters:\n\n";

        foreach ($matches as $index => $match) {
            $chapterUrl = $match[1];
            $chapterTitle = trim($match[2]);

            $chapters[] = [
                'number' => $index + 1,
                'title' => $chapterTitle,
                'url' => $chapterUrl
            ];

            echo "  " . ($index + 1) . ". $chapterTitle => $chapterUrl\n";
        }
    } else {
        echo "No chapters found with primary pattern. Trying alternative...\n";

        // Alternative pattern with double quotes
        if (preg_match_all('/<div class=["\']item_ch["\']><a[^>]*href=["\']([^"\']+)["\'][^>]*>([^<]+)<\/a><\/div>/i', $html, $matches, PREG_SET_ORDER)) {
            echo "Found " . count($matches) . " chapters (alternative pattern):\n\n";

            foreach ($matches as $index => $match) {
                $chapterUrl = $match[1];
                $chapterTitle = trim($match[2]);

                $chapters[] = [
                    'number' => $index + 1,
                    'title' => $chapterTitle,
                    'url' => $chapterUrl
                ];

                echo "  " . ($index + 1) . ". $chapterTitle => $chapterUrl\n";
            }
        }
    }

    echo "\nTotal chapters found: " . count($chapters) . "\n\n";

    // STEP 2: Fetch first chapter content to analyze structure
    echo "STEP 2: Analyzing chapter content structure\n";
    echo "============================================\n\n";

    if (!empty($chapters)) {
        $firstChapter = $chapters[0];
        echo "Fetching first chapter: {$firstChapter['title']}\n";
        echo "URL: {$firstChapter['url']}\n\n";

        $chapterResponse = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ])->timeout(30)->get($firstChapter['url']);

        if ($chapterResponse->successful()) {
            $chapterHtml = $chapterResponse->body();

            // Save for analysis
            file_put_contents('nhasachmienphi_chapter.html', $chapterHtml);
            echo "✓ Saved chapter HTML to nhasachmienphi_chapter.html\n\n";

            // Look for h2.mg-t-10 (chapter title)
            echo "Looking for chapter title (h2.mg-t-10):\n";
            if (preg_match('/<h2[^>]*class=["\'][^"\']*mg-t-10[^"\']*["\'][^>]*>([^<]+)<\/h2>/i', $chapterHtml, $titleMatch)) {
                echo "  ✓ Found: " . trim($titleMatch[1]) . "\n\n";
            } else {
                echo "  ✗ Not found\n\n";
            }

            // Look for div.pd-lr-30 (content)
            echo "Looking for content (div.pd-lr-30):\n";
            if (preg_match('/<div[^>]*class=["\'][^"\']*pd-lr-30[^"\']*["\'][^>]*>(.*?)<\/div>/is', $chapterHtml, $contentMatch)) {
                $content = strip_tags($contentMatch[1]);
                $content = preg_replace('/\s+/', ' ', $content);
                echo "  ✓ Found content! Length: " . strlen($content) . " chars\n";
                echo "  Preview (first 500 chars):\n";
                echo "  " . substr(trim($content), 0, 500) . "...\n\n";
            } else {
                echo "  ✗ Not found with pd-lr-30. Looking for alternatives...\n";

                // Try to find the main content container
                if (preg_match('/<div[^>]*class=["\'][^"\']*content[^"\']*["\'][^>]*>(.*?)<\/div>/is', $chapterHtml, $altMatch)) {
                    $content = strip_tags($altMatch[1]);
                    echo "  ✓ Found 'content' div. Preview:\n";
                    echo "  " . substr(trim($content), 0, 500) . "...\n\n";
                }
            }

            // Look for specific structure in the chapter page
            echo "Searching for common content containers...\n";

            // Check for article tag
            if (preg_match('/<article[^>]*>(.*?)<\/article>/is', $chapterHtml, $articleMatch)) {
                echo "  ✓ Found <article> tag\n";
            }

            // Check for #noi-dung or .noi-dung
            if (preg_match('/<div[^>]*(?:id|class)=["\'][^"\']*noi-dung[^"\']*["\'][^>]*>/i', $chapterHtml)) {
                echo "  ✓ Found noi-dung div\n";
            }

            // Check for specific patterns in the HTML
            $patterns = ['box_cont', 'chap_content', 'novel-content', 'chapter-content', 'story-content'];
            foreach ($patterns as $pattern) {
                if (stripos($chapterHtml, $pattern) !== false) {
                    echo "  ✓ Found class/id containing '$pattern'\n";
                }
            }
        } else {
            echo "✗ Failed to fetch chapter\n";
        }
    }

    // Show summary
    echo "\n\nSUMMARY\n";
    echo "=======\n";
    echo "Book URL structure: https://nhasachmienphi.com/{book-slug}.html\n";
    echo "Chapter URL structure: https://nhasachmienphi.com/doc-online/{book-slug}-{chapter-id}\n";
    echo "Chapter list container: <div class='box_chhr'>\n";
    echo "Chapter item: <div class='item_ch'><a href='...'>{title}</a></div>\n";
    echo "Chapter title: <h2 class='mg-t-10'>\n";
    echo "Chapter content: <div class='pd-lr-30'>\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
