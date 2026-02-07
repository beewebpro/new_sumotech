<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id')->unique();
            $table->string('title');
            $table->string('custom_url')->nullable();
            $table->text('description')->nullable();
            $table->string('country', 2)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->unsignedBigInteger('subscribers_count')->nullable();
            $table->unsignedBigInteger('videos_count')->nullable();
            $table->unsignedBigInteger('views_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_channels');
    }
};
