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
        Schema::create('audiobook_chapter_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audiobook_chapter_id')->constrained('audiobook_chapters')->onDelete('cascade');
            $table->integer('chunk_number');
            $table->longText('text_content');
            $table->string('audio_file')->nullable();
            $table->integer('duration')->default(0); // seconds
            $table->enum('status', ['pending', 'processing', 'completed', 'error'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index('audiobook_chapter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiobook_chapter_chunks');
    }
};
