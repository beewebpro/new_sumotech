<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChannelSpeaker;

$speaker = ChannelSpeaker::find(1);

if (!$speaker) {
    echo "❌ Speaker not found!\n";
    exit(1);
}

echo "✅ Speaker Information:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ID: {$speaker->id}\n";
echo "Name: {$speaker->name}\n";
echo "Gender: {$speaker->gender}\n";
echo "\n📸 Avatar:\n";
echo "  Field value: {$speaker->avatar}\n";
echo "  Public URL: {$speaker->avatar_url}\n";
echo "\n🎬 Lip-sync:\n";
echo "  Enabled: " . ($speaker->lip_sync_enabled ? '✅ Yes' : '❌ No') . "\n";
echo "\n";

// Test if URL is accessible
echo "🔍 Testing URL accessibility...\n";
$headers = @get_headers($speaker->avatar_url);
if ($headers && strpos($headers[0], '200')) {
    echo "✅ URL is accessible (HTTP 200)\n";
} else {
    echo "⚠️  Could not verify URL (might be OK, depends on CORS)\n";
}

echo "\n🎯 Ready to generate lip-sync video!\n";
