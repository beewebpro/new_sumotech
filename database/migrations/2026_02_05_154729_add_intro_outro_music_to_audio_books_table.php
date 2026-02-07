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
            $table->string('intro_music')->nullable()->after('tts_style_instruction')->comment('Path to intro music file');
            $table->string('outro_music')->nullable()->after('intro_music')->comment('Path to outro music file');
            $table->integer('intro_fade_duration')->default(3)->after('outro_music')->comment('Intro music fade out duration in seconds');
            $table->integer('outro_fade_duration')->default(10)->after('intro_fade_duration')->comment('Outro music fade in duration in seconds');
            $table->integer('outro_extend_duration')->default(5)->after('outro_fade_duration')->comment('Extra seconds after voice ends');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn(['intro_music', 'outro_music', 'intro_fade_duration', 'outro_fade_duration', 'outro_extend_duration']);
        });
    }
};
