<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('youtube_channels', function (Blueprint $table) {
            $table->text('youtube_access_token')->nullable()->after('views_count');
            $table->text('youtube_refresh_token')->nullable()->after('youtube_access_token');
            $table->timestamp('youtube_token_expires_at')->nullable()->after('youtube_refresh_token');
            $table->boolean('youtube_connected')->default(false)->after('youtube_token_expires_at');
            $table->string('youtube_connected_email')->nullable()->after('youtube_connected');
        });
    }

    public function down(): void
    {
        Schema::table('youtube_channels', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_access_token',
                'youtube_refresh_token',
                'youtube_token_expires_at',
                'youtube_connected',
                'youtube_connected_email',
            ]);
        });
    }
};
