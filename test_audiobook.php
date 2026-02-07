<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AudioBook;
use App\Models\AudioBookChapter;

try {
    // Test: Get first audiobook with chapters
    $audioBook = AudioBook::with('chapters')->first();

    if ($audioBook) {
        echo "✓ AudioBook found: {$audioBook->title}\n";
        echo "  Chapters count: {$audioBook->chapters()->count()}\n";

        $chapter = $audioBook->chapters()->first();
        if ($chapter) {
            echo "\n✓ First chapter: {$chapter->title}\n";
            echo "  Chunks count: {$chapter->chunks()->count()}\n";
            echo "  Chunks with 'completed' status: {$chapter->chunks()->where('status', 'completed')->count()}\n";
            echo "\n✓ All queries worked successfully!\n";
        } else {
            echo "No chapters found.\n";
        }
    } else {
        echo "No audiobooks found.\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: {$e->getMessage()}\n";
    echo "  File: {$e->getFile()}:{$e->getLine()}\n";
}
