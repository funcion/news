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
}