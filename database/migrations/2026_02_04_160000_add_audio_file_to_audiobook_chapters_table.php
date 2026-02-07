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
        Schema::table('audiobook_chapters', function (Blueprint $table) {
            $table->string('audio_file')->nullable()->after('total_chunks');
            $table->decimal('total_duration', 10, 2)->nullable()->after('audio_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audiobook_chapters', function (Blueprint $table) {
            $table->dropColumn(['audio_file', 'total_duration']);
        });
    }
};
