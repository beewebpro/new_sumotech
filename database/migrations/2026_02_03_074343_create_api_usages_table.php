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
        Schema::create('api_usages', function (Blueprint $table) {
            $table->id();
            $table->string('api_type'); // OpenAI, ElevenLabs, YouTube, GoogleTTS, etc.
            $table->string('api_endpoint')->nullable(); // Specific endpoint called
            $table->string('purpose'); // generate_transcript, translate, tts, etc.
            $table->text('description')->nullable(); // Additional context
            $table->string('status')->default('success'); // success, failed, pending
            $table->text('error_message')->nullable(); // Error details if failed
            $table->json('request_data')->nullable(); // Request parameters (optional)
            $table->json('response_data')->nullable(); // Response data (optional)
            $table->decimal('estimated_cost', 10, 6)->default(0); // USD
            $table->integer('tokens_used')->nullable(); // For OpenAI/LLM APIs
            $table->integer('characters_used')->nullable(); // For TTS APIs
            $table->decimal('duration_seconds', 10, 2)->nullable(); // For audio/video processing
            $table->unsignedBigInteger('project_id')->nullable(); // Link to project if applicable
            $table->unsignedBigInteger('user_id')->nullable(); // User who triggered the API call
            $table->string('ip_address')->nullable(); // IP address
            $table->timestamps();

            // Indexes
            $table->index('api_type');
            $table->index('purpose');
            $table->index('status');
            $table->index('project_id');
            $table->index('user_id');
            $table->index('created_at');

            // Foreign keys (optional, add if you want constraints)
            // $table->foreign('project_id')->references('id')->on('dub_sync_projects')->onDelete('set null');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_usages');
    }
};
