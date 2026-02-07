<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\AudioBookChapter;
use Illuminate\Support\Facades\DB;

try {
    // Check if there are any chapters at all
    $totalChapters = AudioBookChapter::count();
    echo "Total chapters in database: $totalChapters\n";

    // Check the chunks table
    $totalChunks = DB::table('audiobook_chapter_chunks')->count();
    echo "Total chunks in database: $totalChunks\n";

    // Try to get any chapter and access its chunks
    $chapter = AudioBookChapter::first();

    if ($chapter) {
        echo "\n✓ Found chapter: {$chapter->title} (ID: {$chapter->id})\n";

        // This is where the error was happening
        $chunkCount = $chapter->chunks()->count();
        echo "✓ Chunks count for this chapter: {$chunkCount}\n";

        $completedCount = $chapter->chunks()->where('status', 'completed')->count();
        echo "✓ Completed chunks: {$completedCount}\n";

        echo "\n✓ All queries executed successfully! The fix works.\n";
    } else {
        echo "No chapters found. Database might be empty.\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: {$e->getMessage()}\n";
    echo "  File: {$e->getFile()}:{$e->getLine()}\n";
    echo "  SQL: " . $e->getPrevious()?->getMessage() . "\n";
}
