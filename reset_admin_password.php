<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'admin@retcrm.com')->first();

if ($user) {
    $user->password = Hash::make('123@abc');
    $user->save();
    echo "✓ Password đã được reset thành công!\n";
    echo "  Email: admin@retcrm.com\n";
    echo "  Password mới: 123@abc\n";
} else {
    echo "✗ Không tìm thấy user admin\n";
}
