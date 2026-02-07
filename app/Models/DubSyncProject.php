<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DubSyncProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'youtube_channel_id',
        'video_id',
        'youtube_url',
        'youtube_title',
        'youtube_title_vi',
        'youtube_description',
        'youtube_description_vi',
        'youtube_thumbnail',
        'youtube_duration',
        'original_transcript',
        'segments',
        'translated_segments',
        'audio_segments',
        'aligned_segments',
        'final_audio_path',
        'exported_files',
        'status',
        'tts_provider',
        'audio_mode',
        'speakers_config',
        'style_instruction',
        'full_transcript',
        'translated_full_transcript',
        'full_transcript_audio_files',
        'full_transcript_merged_file'
    ];

    protected $casts = [
        'original_transcript' => 'array',
        'segments' => 'array',
        'translated_segments' => 'array',
        'audio_segments' => 'array',
        'aligned_segments' => 'array',
        'exported_files' => 'array',
        'speakers_config' => 'array',
        'full_transcript_audio_files' => 'array',
        'full_transcript_merged_file' => 'array',
    ];

    /**
     * Get the user that owns the project
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function youtubeChannel()
    {
        return $this->belongsTo(YoutubeChannel::class, 'youtube_channel_id');
    }
}
