<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DubSyncProject;

$projects = DubSyncProject::where('status', '!=', null)->get();

echo "Processing " . $projects->count() . " projects...\n";
$fixed = 0;

foreach ($projects as $project) {
    if (empty($project->segments)) {
        continue;
    }

    $segments = $project->segments;
    $firstSegmentStartTime = $segments[0]['start_time'] ?? 0;

    // If first segment doesn't start at 0, we need to adjust all timestamps
    if ($firstSegmentStartTime != 0) {
        $offset = $firstSegmentStartTime;

        foreach ($segments as &$segment) {
            if (isset($segment['start_time'])) {
                $segment['start_time'] -= $offset;
            }

            if (isset($segment['end_time'])) {
                $segment['end_time'] -= $offset;
            }

            if (isset($segment['entries']) && is_array($segment['entries'])) {
                foreach ($segment['entries'] as &$entry) {
                    if (isset($entry['start'])) {
                        $entry['start'] -= $offset;
                    }
                }
            }
        }

        $project->update(['segments' => $segments]);
        echo "Fixed Project #{$project->id}: adjusted timestamps by -{$offset}s\n";
        $fixed++;
    }
}

echo "Done! Fixed $fixed projects.\n";
