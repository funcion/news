<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'raw_article_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'author_id',
        'category_id',
        'image_url',
        'image_alt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'published_at',
        'views',
        'reading_time',
        'seo_score',
        'ai_metadata',
        'embedding',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
        'reading_time' => 'integer',
        'seo_score' => 'integer',
        'meta_keywords' => 'array',
        'ai_metadata' => 'array',
        'embedding' => 'array',
    ];

    /**
     * Get the raw article that this article was created from.
     */
    public function rawArticle()
    {
        return $this->belongsTo(RawArticle::class);
    }

    /**
     * Get the author of the article.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get the category of the article.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags for the article.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')
            ->withPivot('relevance_score')
            ->withTimestamps();
    }

    /**
     * Get the updates for this article.
     */
    public function updates()
    {
        return $this->hasMany(ArticleUpdate::class);
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft articles.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include pending review articles.
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    /**
     * Scope a query to only include articles with high SEO score.
     */
    public function scopeHighSeoScore($query, $threshold = 80)
    {
        return $query->where('seo_score', '>=', $threshold);
    }

    /**
     * Get the URL for the article.
     */
    public function getUrlAttribute(): string
    {
        return route('articles.show', $this->slug);
    }

    /**
     * Increment the view count.
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Calculate the reading time in minutes.
     */
    public function calculateReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // 200 words per minute
    }

    /**
     * Get related articles based on tags.
     */
    public function getRelatedArticles($limit = 5)
    {
        $tagIds = $this->tags()->pluck('tags.id');
        
        return self::published()
            ->where('id', '!=', $this->id)
            ->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
            ->withCount(['tags as common_tags_count' => function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            }])
            ->orderByDesc('common_tags_count')
            ->limit($limit)
            ->get();
    }
}