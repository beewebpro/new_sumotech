<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChannelSpeaker;
use Illuminate\Support\Facades\Log;

// Avatar URL từ Cloudinary
$newAvatarUrl = 'https://res.cloudinary.com/dye2qjfo5/image/upload/v1770366866/sumo_female_16_9_licvp2.jpg';

// Tìm speaker có avatar cũ chứa local path
$speakers = ChannelSpeaker::where('avatar', 'like', '%speakers/%')
    ->get();

if ($speakers->isEmpty()) {
    echo "❌ Không tìm thấy speaker nào có avatar local.\n";
    echo "\n📋 Danh sách tất cả speakers:\n";

    $allSpeakers = ChannelSpeaker::all();
    foreach ($allSpeakers as $speaker) {
        echo "ID {$speaker->id}: {$speaker->name}\n";
        echo "   Avatar field: " . ($speaker->avatar ?: 'chưa có') . "\n";
        echo "   Avatar URL: " . ($speaker->avatar_url ?: 'chưa có') . "\n";
        echo "   Lip-sync: " . ($speaker->lip_sync_enabled ? 'Enabled' : 'Disabled') . "\n\n";
    }

    echo "\n💡 Nhập ID của speaker cần update (hoặc 'all' để update tất cả): ";
    $input = trim(fgets(STDIN));

    if ($input === 'all') {
        $speakers = $allSpeakers;
    } else {
        $speakerId = (int)$input;
        $speaker = ChannelSpeaker::find($speakerId);
        if (!$speaker) {
            echo "❌ Không tìm thấy speaker ID {$speakerId}\n";
            exit(1);
        }
        $speakers = collect([$speaker]);
    }
}

echo "\n🔄 Sẽ update avatar URL cho " . $speakers->count() . " speaker(s):\n\n";

foreach ($speakers as $speaker) {
    $oldAvatar = $speaker->avatar;

    echo "Speaker ID {$speaker->id}: {$speaker->name}\n";
    echo "  Old: " . ($oldAvatar ?: 'chưa có') . "\n";
    echo "  New: {$newAvatarUrl}\n";

    // Update avatar field với full Cloudinary URL
    $speaker->avatar = $newAvatarUrl;

    // Đảm bảo lip_sync_enabled = true
    if (!$speaker->lip_sync_enabled) {
        echo "  ⚠️  Enabling lip-sync...\n";
        $speaker->lip_sync_enabled = true;
    }

    $speaker->save();

    Log::info("Updated speaker avatar to Cloudinary", [
        'speaker_id' => $speaker->id,
        'speaker_name' => $speaker->name,
        'old_avatar' => $oldAvatar,
        'new_avatar' => $newAvatarUrl
    ]);

    echo "  ✅ Updated successfully!\n\n";
}

echo "\n✨ Hoàn thành! Avatar URL đã được update.\n";
echo "🎬 Bạn có thể test tạo video lip-sync ngay bây giờ.\n\n";

echo "📝 Để verify, chạy:\n";
echo "   php artisan tinker\n";
echo "   >>> \\App\\Models\\ChannelSpeaker::find({$speakers->first()->id})->avatar_url;\n\n";
