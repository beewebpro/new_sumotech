<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::first();

if ($user) {
    $user->role = 'admin';
    $user->save();
    echo "User {$user->email} đã được cập nhật thành admin\n";
} else {
    echo "Không tìm thấy user nào trong database\n";
}
