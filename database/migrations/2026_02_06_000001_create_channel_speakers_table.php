<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('youtube_channel_id')->constrained('youtube_channels')->cascadeOnDelete();
            $table->string('name'); // Tên MC/người thuyết minh
            $table->string('avatar')->nullable(); // Đường dẫn file avatar
            $table->text('description')->nullable(); // Mô tả về MC
            $table->string('gender')->default('female'); // male/female
            $table->string('voice_style')->nullable(); // Phong cách giọng nói
            $table->string('default_voice_provider')->nullable(); // openai/gemini/microsoft
            $table->string('default_voice_name')->nullable(); // Tên giọng mặc định
            $table->boolean('is_active')->default(true); // Trạng thái hoạt động
            $table->boolean('lip_sync_enabled')->default(false); // Bật hiệu ứng nhép miệng
            $table->json('lip_sync_settings')->nullable(); // Cài đặt lip-sync (sensitivity, style...)
            $table->json('additional_images')->nullable(); // Các hình ảnh bổ sung (khung cảnh, pose khác...)
            $table->timestamps();
        });

        // Add speaker_id to audio_books table
        Schema::table('audio_books', function (Blueprint $table) {
            $table->foreignId('speaker_id')->nullable()->after('youtube_channel_id')->constrained('channel_speakers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('audio_books', function (Blueprint $table) {
            $table->dropForeign(['speaker_id']);
            $table->dropColumn('speaker_id');
        });

        Schema::dropIfExists('channel_speakers');
    }
};
