<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\BookScrapers\NhaSachMienPhiScraper;

$url = 'https://nhasachmienphi.com/diep-vien-007-song-bac-hoang-gia.html';
$scraper = new NhaSachMienPhiScraper($url);

echo "=== Testing NhaSachMienPhiScraper ===\n";
echo "URL: $url\n\n";

$result = $scraper->scrape();

if (isset($result['error'])) {
    echo "ERROR: " . $result['error'] . "\n";
} else {
    echo "Book Title: " . $result['title'] . "\n";
    echo "Total Chapters: " . $result['total_chapters'] . "\n\n";

    echo "=== All Chapters ===\n";
    foreach ($result['chapters'] as $ch) {
        echo $ch['number'] . ". " . $ch['title'] . "\n";
        echo "   URL: " . $ch['url'] . "\n";
    }

    if ($result['total_chapters'] > 0) {
        echo "\n=== Testing Chapter Content - Chapter 1 ===\n";
        $chapterUrl = $result['chapters'][0]['url'];
        $content = $scraper->scrapeChapterContent($chapterUrl);
        $title = $scraper->scrapeChapterTitle($chapterUrl);

        echo "Chapter Title: " . $title . "\n";
        echo "Content Length: " . strlen($content) . " characters\n";
        echo "Content Preview (first 500 chars):\n";
        echo "---\n";
        echo mb_substr($content, 0, 500, 'UTF-8') . "...\n";
        echo "---\n";
    }
}
