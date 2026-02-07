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
            $table->string('youtube_title_vi')->nullable()->after('youtube_title');
            $table->text('youtube_description_vi')->nullable()->after('youtube_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->dropColumn([
                'youtube_title_vi',
                'youtube_description_vi'
            ]);
        });
    }
};
