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
        Schema::table('audio_books', function (Blueprint $table) {
            $table->string('description_lipsync_video')->nullable()->after('description_audio_duration');
            $table->integer('description_lipsync_duration')->nullable()->after('description_lipsync_video');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn(['description_lipsync_video', 'description_lipsync_duration']);
        });
    }
};
