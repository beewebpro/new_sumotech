<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select('SHOW COLUMNS FROM audiobook_chapter_chunks');

echo "Columns in audiobook_chapter_chunks:\n";
echo "=====================================\n";
foreach ($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}
