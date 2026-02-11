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
        // Check if columns don't exist before adding
        Schema::table('audio_books', function (Blueprint $table) {
            if (!Schema::hasColumn('audio_books', 'youtube_playlist_id')) {
                $table->string('youtube_playlist_id')->nullable()->after('youtube_channel_id')
                    ->comment('Selected or created YouTube playlist ID');
            }
            if (!Schema::hasColumn('audio_books', 'youtube_playlist_title')) {
                $table->string('youtube_playlist_title')->nullable()->after('youtube_playlist_id')
                    ->comment('YouTube playlist title');
            }
            if (!Schema::hasColumn('audio_books', 'youtube_video_title')) {
                $table->text('youtube_video_title')->nullable()->after('youtube_playlist_title')
                    ->comment('AI-generated video title for main/single uploads');
            }
            if (!Schema::hasColumn('audio_books', 'youtube_video_description')) {
                $table->text('youtube_video_description')->nullable()->after('youtube_video_title')
                    ->comment('AI-generated video description');
            }
            if (!Schema::hasColumn('audio_books', 'youtube_video_tags')) {
                $table->text('youtube_video_tags')->nullable()->after('youtube_video_description')
                    ->comment('Comma-separated video tags');
            }
        });

        Schema::table('audiobook_chapters', function (Blueprint $table) {
            if (!Schema::hasColumn('audiobook_chapters', 'youtube_video_id')) {
                $table->string('youtube_video_id')->nullable()->after('video_path')
                    ->comment('YouTube video ID after upload');
            }
            if (!Schema::hasColumn('audiobook_chapters', 'youtube_video_title')) {
                $table->string('youtube_video_title')->nullable()->after('youtube_video_id')
                    ->comment('Per-chapter AI-generated video title');
            }
            if (!Schema::hasColumn('audiobook_chapters', 'youtube_video_description')) {
                $table->text('youtube_video_description')->nullable()->after('youtube_video_title')
                    ->comment('Per-chapter AI-generated video description');
            }
            if (!Schema::hasColumn('audiobook_chapters', 'youtube_uploaded_at')) {
                $table->timestamp('youtube_uploaded_at')->nullable()->after('youtube_video_description')
                    ->comment('Timestamp when uploaded to YouTube');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_playlist_id',
                'youtube_playlist_title',
                'youtube_video_title',
                'youtube_video_description',
                'youtube_video_tags'
            ]);
        });

        Schema::table('audiobook_chapters', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_video_id',
                'youtube_video_title',
                'youtube_video_description',
                'youtube_uploaded_at'
            ]);
        });
    }
};
