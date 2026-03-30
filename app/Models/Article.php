<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Article extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTranslations;

    /**
     * The translatable fields — stored as {"en": "...", "es": "..."} in JSONB.
     */
    public array $translatable = [
        'title',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'image_alt',
    ];

    protected $fillable = [
        'raw_article_id',
        'title',
        'slug_en',
        'slug_es',
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
     * Get the slug for the current locale.
     */
    public function getSlugAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'es' ? ($this->slug_es ?? $this->slug_en ?? '') : ($this->slug_en ?? '');
    }

    public function rawArticle()
    {
        return $this->belongsTo(RawArticle::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')
            ->withPivot('relevance_score')
            ->withTimestamps();
    }

    public function updates()
    {
        return $this->hasMany(ArticleUpdate::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeHighSeoScore($query, $threshold = 80)
    {
        return $query->where('seo_score', '>=', $threshold);
    }

    /**
     * Get the URL for the article using locale-aware slug.
     */
    public function getUrlAttribute(): string
    {
        $locale = app()->getLocale();
        $slug = $locale === 'es' ? ($this->slug_es ?? $this->slug_en) : ($this->slug_en ?? $this->slug_es);
        return route('articles.show', ['slug' => $slug]);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function calculateReadingTime(): int
    {
        $content = $this->getTranslation('content', 'en') ?? $this->getTranslation('content', 'es') ?? '';
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200));
    }

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

    /**
     * Register responsive media conversions (WebP, 3 sizes).
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(480)->height(270)->sharpen(10)->format('webp')->nonQueued();

        $this->addMediaConversion('medium')
            ->width(800)->height(450)->sharpen(5)->format('webp')->nonQueued();

        $this->addMediaConversion('large')
            ->width(1200)->height(675)->format('webp')->nonQueued();
    }
}