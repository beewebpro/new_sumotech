<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$book = App\Models\AudioBook::find(6);
echo "Total chapters: " . $book->chapters()->count() . "\n\n";

foreach ($book->chapters()->orderBy('chapter_number')->get() as $ch) {
    $videoExists = $ch->video_path ? (file_exists(storage_path('app/public/' . $ch->video_path)) ? 'FILE_EXISTS' : 'FILE_MISSING') : 'NO_PATH';
    $coverExists = $ch->cover_image ? (file_exists(storage_path('app/public/' . $ch->cover_image)) ? 'YES' : 'MISSING') : 'NO';
    $audioExists = $ch->audio_file ? 'YES' : 'NO';
    echo "Ch {$ch->chapter_number} (id={$ch->id}): video_path=" . ($ch->video_path ?: 'NULL') . " [{$videoExists}] | cover={$coverExists} | audio={$audioExists}\n";
}

echo "\ndesc_scene: " . ($book->description_scene_video ?: 'NULL') . "\n";
echo "desc_lipsync: " . ($book->description_lipsync_video ?: 'NULL') . "\n";
