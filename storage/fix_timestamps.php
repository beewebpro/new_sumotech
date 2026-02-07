$projects = App\Models\DubSyncProject::where('status', '!=', null)->get();
echo "Processing " . count($projects) . " projects...\n";

foreach ($projects as $project) {
if (empty($project->segments)) {
continue;
}

$segments = $project->segments;
$firstSegmentStartTime = $segments[0]['start_time'] ?? 0;

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
}
}

echo "Done!\n";