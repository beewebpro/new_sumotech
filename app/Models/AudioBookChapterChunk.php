<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioBookChapterChunk extends Model
{
    use HasFactory;

    protected $table = 'audiobook_chapter_chunks';

    protected $fillable = [
        'audiobook_chapter_id',
        'chunk_number',
        'text_content',
        'audio_file',
        'duration',
        'status',
        'error_message'
    ];

    public function chapter()
    {
        return $this->belongsTo(AudioBookChapter::class, 'audiobook_chapter_id');
    }
}
