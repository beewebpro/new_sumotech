<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioBookVideoSegment extends Model
{
    use HasFactory;

    protected $table = 'audiobook_video_segments';

    protected $fillable = [
        'audio_book_id',
        'name',
        'chapters',
        'image_path',
        'image_type',
        'video_path',
        'video_duration',
        'status',
        'error_message',
        'sort_order',
        'youtube_video_id',
        'youtube_video_title',
        'youtube_video_description',
        'youtube_uploaded_at',
    ];

    protected $casts = [
        'chapters' => 'array',
        'video_duration' => 'float',
        'youtube_uploaded_at' => 'datetime',
    ];

    public function audioBook()
    {
        return $this->belongsTo(AudioBook::class);
    }
}
