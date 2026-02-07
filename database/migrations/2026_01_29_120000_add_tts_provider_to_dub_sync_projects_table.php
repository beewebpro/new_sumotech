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
            $table->string('tts_provider')->default('google')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->dropColumn('tts_provider');
        });
    }
};
