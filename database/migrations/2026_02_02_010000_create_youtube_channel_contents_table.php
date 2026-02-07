<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_channel_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('youtube_channel_id')->constrained('youtube_channels')->cascadeOnDelete();
            $table->string('video_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('views_count')->nullable();
            $table->unsignedBigInteger('likes_count')->nullable();
            $table->unsignedBigInteger('comments_count')->nullable();
            $table->timestamps();

            $table->index(['youtube_channel_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_channel_contents');
    }
};
