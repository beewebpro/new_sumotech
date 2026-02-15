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
            $table->string('full_book_video')->nullable()->after('description_scene_video_duration');
            $table->float('full_book_video_duration')->nullable()->after('full_book_video');
        });
    }

    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropColumn(['full_book_video', 'full_book_video_duration']);
        });
    }
};
