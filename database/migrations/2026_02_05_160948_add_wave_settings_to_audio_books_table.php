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
            $table->boolean('wave_enabled')->default(false)->after('outro_extend_duration');
            // Wave types: line, p2p, cline, point, bar (from FFmpeg showwaves)
            $table->string('wave_type', 20)->default('cline')->after('wave_enabled');
            // Position: top, center, bottom
            $table->string('wave_position', 20)->default('bottom')->after('wave_type');
            // Wave height in pixels (e.g., 100, 150, 200)
            $table->integer('wave_height')->default(100)->after('wave_position');
            // Wave color in hex format (e.g., #00ff00)
            $table->string('wave_color', 20)->default('#00ff00')->after('wave_height');
            // Wave opacity (0.0 to 1.0)
            $table->decimal('wave_opacity', 3, 2)->default(0.8)->after('wave_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn([
                'wave_enabled',
                'wave_type',
                'wave_position',
                'wave_height',
                'wave_color',
                'wave_opacity'
            ]);
        });
    }
};
