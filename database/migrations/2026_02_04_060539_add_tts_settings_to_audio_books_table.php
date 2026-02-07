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
            $table->string('tts_provider')->nullable()->after('total_chapters');
            $table->string('tts_voice_gender')->nullable()->after('tts_provider');
            $table->string('tts_voice_name')->nullable()->after('tts_voice_gender');
            $table->text('tts_style_instruction')->nullable()->after('tts_voice_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn(['tts_provider', 'tts_voice_gender', 'tts_voice_name', 'tts_style_instruction']);
        });
    }
};
