<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command');
            $table->string('command_name');
            $table->string('status');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->decimal('duration_seconds', 10, 2)->nullable();
            $table->text('output')->nullable();
            $table->text('error_message')->nullable();
            $table->json('meta_data')->nullable();
            $table->string('trigger')->default('schedule');
            $table->timestamps();

            $table->index('command_name');
            $table->index('status');
            $table->index('started_at');
            $table->index('trigger');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_logs');
    }
};
