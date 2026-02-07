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
        Schema::create('dub_sync_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('video_id');
            $table->string('youtube_url');
            $table->text('original_transcript')->nullable();
            $table->text('segments')->nullable();
            $table->text('translated_segments')->nullable();
            $table->text('audio_segments')->nullable();
            $table->text('aligned_segments')->nullable();
            $table->string('final_audio_path')->nullable();
            $table->text('exported_files')->nullable();
            $table->string('status')->default('pending'); // pending, transcribed, translated, tts_generated, aligned, merged, completed
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dub_sync_projects');
    }
};
