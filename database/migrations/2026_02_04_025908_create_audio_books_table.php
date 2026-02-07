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
        Schema::create('audio_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('youtube_channel_id')->constrained('youtube_channels')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('language')->default('vi');
            $table->integer('total_chapters')->default(0);
            $table->timestamps();
            $table->index('youtube_channel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_books');
    }
};
