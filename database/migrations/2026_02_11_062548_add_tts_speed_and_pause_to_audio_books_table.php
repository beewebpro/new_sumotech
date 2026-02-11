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
            $table->decimal('tts_speed', 3, 2)->default(1.00)->after('tts_style_instruction');
            $table->decimal('pause_between_chunks', 3, 1)->default(1.0)->after('tts_speed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn(['tts_speed', 'pause_between_chunks']);
        });
    }
};
