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
        Schema::create('audiobook_video_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audio_book_id')->constrained('audio_books')->cascadeOnDelete();
            $table->string('name');
            $table->json('chapters');
            $table->string('image_path')->nullable();
            $table->string('image_type')->nullable();
            $table->string('video_path')->nullable();
            $table->float('video_duration')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiobook_video_segments');
    }
};
