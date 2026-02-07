<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeChannelContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'youtube_channel_id',
        'video_id',
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'duration_seconds',
        'published_at',
        'views_count',
        'likes_count',
        'comments_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function channel()
    {
        return $this->belongsTo(YoutubeChannel::class, 'youtube_channel_id');
    }
}
