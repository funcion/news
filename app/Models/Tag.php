<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'article_count',
        'is_featured',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'article_count' => 'integer',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the articles for the tag.
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag')
            ->withPivot('relevance_score')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include featured tags.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include popular tags.
     */
    public function scopePopular($query, $limit = 20)
    {
        return $query->orderByDesc('article_count')->limit($limit);
    }

    /**
     * Scope a query to only include tags with minimum article count.
     */
    public function scopeWithMinimumArticles($query, $minCount = 1)
    {
        return $query->where('article_count', '>=', $minCount);
    }

    /**
     * Get related tags based on co-occurrence.
     */
    public function getRelatedTags($limit = 10)
    {
        $articleIds = $this->articles()->pluck('articles.id');
        
        return self::where('id', '!=', $this->id)
            ->whereHas('articles', function ($query) use ($articleIds) {
                $query->whereIn('articles.id', $articleIds);
            })
            ->withCount(['articles as common_articles_count' => function ($query) use ($articleIds) {
                $query->whereIn('articles.id', $articleIds);
            }])
            ->orderByDesc('common_articles_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Increment the article count.
     */
    public function incrementArticleCount(): void
    {
        $this->increment('article_count');
    }

    /**
     * Decrement the article count.
     */
    public function decrementArticleCount(): void
    {
        $this->decrement('article_count');
    }

    /**
     * Get the URL for the tag.
     */
    public function getUrlAttribute(): string
    {
        return route('tags.show', $this->slug);
    }

    /**
     * Normalize tag name.
     */
    public static function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));
        
        // Remove special characters except hyphens
        $name = preg_replace('/[^a-z0-9\s-]/', '', $name);
        
        // Replace multiple spaces with single space
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Convert spaces to hyphens
        $name = str_replace(' ', '-', $name);
        
        return $name;
    }

    /**
     * Find or create a tag by name.
     */
    public static function findOrCreateByName(string $name, array $attributes = []): self
    {
        $normalizedName = self::normalizeName($name);
        $slug = $normalizedName;
        
        return self::firstOrCreate(
            ['slug' => $slug],
            array_merge($attributes, ['name' => $name])
        );
    }
}