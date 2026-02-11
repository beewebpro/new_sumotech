<?php

namespace App\Mail;

use App\Models\AudioBook;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AudiobookProcessingComplete extends Mailable
{
    use Queueable, SerializesModels;

    public AudioBook $audioBook;
    public string $type; // 'audio' or 'video'
    public array $stats;

    public function __construct(AudioBook $audioBook, string $type, array $stats = [])
    {
        $this->audioBook = $audioBook;
        $this->type = $type;
        $this->stats = $stats;
    }

    public function envelope(): Envelope
    {
        $typeLabel = $this->type === 'audio' ? 'Audio' : 'Video';
        return new Envelope(
            subject: "[SumoTech] {$typeLabel} hoàn thành: {$this->audioBook->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.audiobook-complete',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
