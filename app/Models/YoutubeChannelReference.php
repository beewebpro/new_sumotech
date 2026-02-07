<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeChannelReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'youtube_channel_id',
        'ref_channel_url',
        'ref_channel_id',
        'ref_title',
        'ref_description',
        'ref_thumbnail_url',
        'fetch_interval_days',
    ];

    public function channel()
    {
        return $this->belongsTo(YoutubeChannel::class, 'youtube_channel_id');
    }
}
