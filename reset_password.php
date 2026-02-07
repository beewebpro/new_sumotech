<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'admin@sumotech.ai';
$newPassword = '123@abc';

$user = User::where('email', $email)->first();

if ($user) {
    $user->password = Hash::make($newPassword);
    $user->save();
    echo "✓ Password cho {$email} đã được reset thành công\n";
    echo "  Password mới: {$newPassword}\n";
} else {
    echo "✗ Không tìm thấy user với email: {$email}\n";
}
