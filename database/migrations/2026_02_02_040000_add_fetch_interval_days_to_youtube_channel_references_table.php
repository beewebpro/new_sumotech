<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('youtube_channel_references', function (Blueprint $table) {
            $table->unsignedInteger('fetch_interval_days')->default(7)->after('ref_thumbnail_url');
        });
    }

    public function down(): void
    {
        Schema::table('youtube_channel_references', function (Blueprint $table) {
            $table->dropColumn('fetch_interval_days');
        });
    }
};
