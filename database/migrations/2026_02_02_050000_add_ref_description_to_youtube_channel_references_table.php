<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('youtube_channel_references', function (Blueprint $table) {
            $table->text('ref_description')->nullable()->after('ref_title');
        });
    }

    public function down(): void
    {
        Schema::table('youtube_channel_references', function (Blueprint $table) {
            $table->dropColumn('ref_description');
        });
    }
};
