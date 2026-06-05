<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RawArticle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'source_id',
        'title',
        'url',
        'content',
        'summary',
        'author',
        'published_at',
        'categories',
        'image_url',
        'language',
        'hash',
        'metadata',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'categories' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the source that owns the raw article.
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * Get the processed article.
     */
    public function article()
    {
        return $this->hasOne(Article::class);
    }

    /**
     * Scope a query to only include pending articles.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processed articles.
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Scope a query to only include failed articles.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Calculate the hash of the article content.
     */
    public function calculateHash(): string
    {
        return hash('sha256', $this->title . $this->url . $this->content);
    }

    /**
     * Check if this article is a duplicate.
     */
    public function isDuplicate(): bool
    {
        return self::where('hash', $this->hash)
            ->where('id', '!=', $this->id)
            ->exists();
    }

    /**
     * Clean and sanitize content automatically on saving.
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = self::sanitizeContent($value);
    }

    /**
     * Clean raw content by removing boilerplate, CSS, JS, images, and converting links to plain text.
     */
    public static function sanitizeContent(?string $content): string
    {
        if (blank($content)) {
            return '';
        }

        // 1. Strip HTML scripts, styles, and iframes
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);

        // 2. Remove markdown images: ![alt](url)
        $content = preg_replace('/\!\[.*?\]\(.*?\)/i', '', $content);
        // Remove HTML images: <img ...>
        $content = preg_replace('/<img\b[^>]*>/i', '', $content);

        // 3. Remove Markdown links, leaving only the anchor text: [Anchor](URL) -> Anchor
        $content = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $content);

        // 4. Remove HTML links, leaving only the text: <a href="...">Anchor</a> -> Anchor
        $content = preg_replace('/<a\b[^>]*>(.*?)<\/a>/is', '$1', $content);

        // 5. Clean up typical boilerplate patterns
        $lines = explode("\n", $content);
        $cleanedLines = [];
        
        $boilerplateKeywords = [
            'subscribe to', 'newsletter', 'privacy policy', 'terms of service', 'cookie policy',
            'share on twitter', 'share on facebook', 'share on linkedin', 'read more', 'click here',
            'all rights reserved', 'copyright', 'follow us on', 'about the author', 'author bio',
            'advertisement', 'sponsored', 'you might also like', 'related articles', 'related topics'
        ];

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            if (empty($trimmedLine)) {
                $cleanedLines[] = '';
                continue;
            }

            $lowerLine = mb_strtolower($trimmedLine);
            
            // Skip short boilerplate lines
            $isBoilerplate = false;
            if (mb_strlen($lowerLine) < 150) {
                foreach ($boilerplateKeywords as $kw) {
                    if (str_contains($lowerLine, $kw)) {
                        $isBoilerplate = true;
                        break;
                    }
                }
            }

            // Skip lines with only divider characters
            if (preg_match('/^[_\-*#|\s]+$/', $trimmedLine)) {
                continue;
            }

            if (!$isBoilerplate) {
                $cleanedLines[] = $line;
            }
        }

        $content = implode("\n", $cleanedLines);

        // 6. Clean multiple empty lines
        $content = preg_replace("/\n{3,}/", "\n\n", $content);

        return trim($content);
    }
}