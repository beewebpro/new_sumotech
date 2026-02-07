<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'title',
        'custom_url',
        'description',
        'country',
        'published_at',
        'thumbnail_url',
        'subscribers_count',
        'videos_count',
        'views_count',
        'youtube_access_token',
        'youtube_refresh_token',
        'youtube_token_expires_at',
        'youtube_connected',
        'youtube_connected_email',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'youtube_token_expires_at' => 'datetime',
        'youtube_connected' => 'boolean',
    ];

    protected $hidden = [
        'youtube_access_token',
        'youtube_refresh_token',
    ];

    /**
     * Check if YouTube API connection is active and token is valid.
     */
    public function isYoutubeConnected(): bool
    {
        return $this->youtube_connected
            && $this->youtube_refresh_token
            && $this->youtube_access_token;
    }

    /**
     * Check if the access token has expired.
     */
    public function isTokenExpired(): bool
    {
        if (!$this->youtube_token_expires_at) return true;
        return now()->gte($this->youtube_token_expires_at);
    }

    public function contents()
    {
        return $this->hasMany(YoutubeChannelContent::class, 'youtube_channel_id');
    }

    public function referenceChannels()
    {
        return $this->hasMany(YoutubeChannelReference::class, 'youtube_channel_id');
    }

    /**
     * Get the speakers (MCs) for this channel.
     */
    public function speakers()
    {
        return $this->hasMany(ChannelSpeaker::class, 'youtube_channel_id');
    }

    /**
     * Get active speakers only.
     */
    public function activeSpeakers()
    {
        return $this->hasMany(ChannelSpeaker::class, 'youtube_channel_id')->where('is_active', true);
    }

    /**
     * Get the audiobooks for this channel.
     */
    public function audioBooks()
    {
        return $this->hasMany(AudioBook::class, 'youtube_channel_id');
    }
}
