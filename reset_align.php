<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DubSyncProject;

// Video ID to reset
$videoId = 'WAWyfikAndA';

$project = DubSyncProject::where('video_id', $videoId)->first();

if (!$project) {
    echo "❌ Project not found with video_id: {$videoId}\n";
    exit(1);
}

echo "📹 Found project: {$project->title}\n";
echo "   Current status: {$project->status}\n";
echo "   Aligned segments: " . (count($project->aligned_segments ?? []) > 0 ? "Yes" : "No") . "\n\n";

// Reset to status before alignment
$project->aligned_segments = null;
$project->status = 'tts_generated'; // or 'segments_ready' depending on your workflow
$project->save();

echo "✅ Reset complete!\n";
echo "   New status: {$project->status}\n";
echo "   Aligned segments: Cleared\n";
