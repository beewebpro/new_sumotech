<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->longText('translated_full_transcript')->nullable()->after('full_transcript');
        });
    }

    public function down(): void
    {
        Schema::table('dub_sync_projects', function (Blueprint $table) {
            $table->dropColumn('translated_full_transcript');
        });
    }
};
