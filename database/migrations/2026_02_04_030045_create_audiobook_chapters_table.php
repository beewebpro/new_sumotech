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
        Schema::create('audiobook_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audio_book_id')->constrained('audio_books')->onDelete('cascade');
            $table->integer('chapter_number');
            $table->string('title');
            $table->longText('content');
            $table->string('cover_image')->nullable();
            $table->string('tts_voice')->default('vi-VN-HoaiMyNeural');
            $table->decimal('tts_speed', 3, 1)->default(1.0);
            $table->integer('total_chunks')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'error'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index('audio_book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiobook_chapters');
    }
};
