<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('youtube_channel_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('youtube_channel_id')->constrained('youtube_channels')->cascadeOnDelete();
            $table->string('ref_channel_url');
            $table->string('ref_channel_id')->nullable();
            $table->string('ref_title')->nullable();
            $table->string('ref_thumbnail_url')->nullable();
            $table->timestamps();

            $table->index(['youtube_channel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('youtube_channel_references');
    }
};
