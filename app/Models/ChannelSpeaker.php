<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelSpeaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'youtube_channel_id',
        'name',
        'avatar',
        'description',
        'gender',
        'voice_style',
        'default_voice_provider',
        'default_voice_name',
        'is_active',
        'lip_sync_enabled',
        'lip_sync_settings',
        'additional_images',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lip_sync_enabled' => 'boolean',
        'lip_sync_settings' => 'array',
        'additional_images' => 'array',
    ];

    /**
     * Get the YouTube channel that owns the speaker.
     */
    public function youtubeChannel()
    {
        return $this->belongsTo(YoutubeChannel::class, 'youtube_channel_id');
    }

    /**
     * Get the audiobooks using this speaker.
     */
    public function audioBooks()
    {
        return $this->hasMany(AudioBook::class, 'speaker_id');
    }

    /**
     * Get the avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            // If avatar is already a full URL (e.g., from Cloudinary), return it directly
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            // Otherwise, it's a local storage path
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Get additional images URLs.
     */
    public function getAdditionalImagesUrlsAttribute()
    {
        if (!$this->additional_images) {
            return [];
        }

        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $this->additional_images);
    }

    /**
     * Scope to get only active speakers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get default lip sync settings.
     */
    public static function getDefaultLipSyncSettings()
    {
        return [
            'sensitivity' => 0.5,
            'style' => 'natural', // natural, exaggerated, subtle
            'mouth_shape' => 'standard', // standard, wide, narrow
            'blink_rate' => 'normal', // slow, normal, fast
            'head_movement' => true,
        ];
    }
}
