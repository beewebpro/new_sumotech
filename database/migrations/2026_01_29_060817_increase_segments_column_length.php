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
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            // Change text columns to longtext to handle large transcripts
            $table->longText('original_transcript')->nullable()->change();
            $table->longText('segments')->nullable()->change();
            $table->longText('translated_segments')->nullable()->change();
            $table->longText('audio_segments')->nullable()->change();
            $table->longText('aligned_segments')->nullable()->change();
            $table->longText('exported_files')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            // Revert to text columns
            $table->text('original_transcript')->nullable()->change();
            $table->text('segments')->nullable()->change();
            $table->text('translated_segments')->nullable()->change();
            $table->text('audio_segments')->nullable()->change();
            $table->text('aligned_segments')->nullable()->change();
            $table->text('exported_files')->nullable()->change();
        });
    }
};
