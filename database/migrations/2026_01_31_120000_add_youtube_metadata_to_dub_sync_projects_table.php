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
            $table->string('youtube_title')->nullable()->after('youtube_url');
            $table->text('youtube_description')->nullable()->after('youtube_title');
            $table->string('youtube_thumbnail')->nullable()->after('youtube_description');
            $table->string('youtube_duration')->nullable()->after('youtube_thumbnail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_title',
                'youtube_description',
                'youtube_thumbnail',
                'youtube_duration'
            ]);
        });
    }
};
