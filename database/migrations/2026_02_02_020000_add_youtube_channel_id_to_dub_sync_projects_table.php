<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->foreignId('youtube_channel_id')
                ->nullable()
                ->after('user_id')
                ->constrained('youtube_channels')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->dropForeign(['youtube_channel_id']);
            $table->dropColumn('youtube_channel_id');
        });
    }
};
