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
            // Add columns to store full transcript audio files list and merged file info
            if (!Schema::hasColumn('dub_sync_projects', 'full_transcript_audio_files')) {
                $table->json('full_transcript_audio_files')->nullable()->after('translated_full_transcript');
            }
            if (!Schema::hasColumn('dub_sync_projects', 'full_transcript_merged_file')) {
                $table->json('full_transcript_merged_file')->nullable()->after('full_transcript_audio_files');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->dropColumn(['full_transcript_audio_files', 'full_transcript_merged_file']);
        });
    }
};
