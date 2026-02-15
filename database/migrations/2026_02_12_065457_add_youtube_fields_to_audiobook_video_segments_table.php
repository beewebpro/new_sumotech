<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audiobook_video_segments', function (Blueprint $table) {
            $table->string('youtube_video_id')->nullable()->after('error_message');
            $table->string('youtube_video_title')->nullable()->after('youtube_video_id');
            $table->text('youtube_video_description')->nullable()->after('youtube_video_title');
            $table->timestamp('youtube_uploaded_at')->nullable()->after('youtube_video_description');
        });
    }

    public function down(): void
    {
        Schema::table('audiobook_video_segments', function (Blueprint $table) {
            $table->dropColumn(['youtube_video_id', 'youtube_video_title', 'youtube_video_description', 'youtube_uploaded_at']);
        });
    }
};
