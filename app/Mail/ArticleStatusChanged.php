<?php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Article $article,
        public string $oldStatus,
        public string $newStatus,
        public ?string $changedBy = null,
    ) {}

    public function envelope(): Envelope
    {
        $statusEmoji = match($this->newStatus) {
            'published'      => '✅',
            'pending_review' => '🟡',
            'rejected'       => '🔴',
            'draft'          => '⬜',
            default          => '📰',
        };

        return new Envelope(
            subject: "{$statusEmoji} Artículo: " . $this->article->getTranslation('title', 'en') . " → {$this->newStatus}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.article-status-changed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
