<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$users = User::all();

echo "Danh sách users trong database:\n";
foreach ($users as $user) {
    echo "  - {$user->email} (ID: {$user->id}, Role: {$user->role})\n";
}
