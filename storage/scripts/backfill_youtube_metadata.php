<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\YouTubeTranscriptService::class);

App\Models\DubSyncProject::whereNull('youtube_title')
    ->orWhereNull('youtube_thumbnail')
    ->chunk(20, function ($projects) use ($service) {
        foreach ($projects as $project) {
            try {
                $meta = $service->getMetadata($project->video_id);
                $project->youtube_title = $meta['title'] ?? null;
                $project->youtube_description = $meta['description'] ?? null;
                $project->youtube_thumbnail = $meta['thumbnail'] ?? null;
                $project->youtube_duration = $meta['duration'] ?? null;
                $project->save();
                echo "Updated {$project->id}\n";
            } catch (Exception $e) {
                echo "Failed {$project->id}: {$e->getMessage()}\n";
            }
        }
    });

echo "Done\n";
