<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Tag extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'article_count',
        'is_featured',
        'metadata',
    ];

    protected $casts = [
        'article_count' => 'integer',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag')
            ->withPivot('relevance_score')
            ->withTimestamps();
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query, $limit = 20)
    {
        return $query->orderByDesc('article_count')->limit($limit);
    }

    public function scopeWithMinimumArticles($query, $minCount = 1)
    {
        return $query->where('article_count', '>=', $minCount);
    }

    public function getUrlAttribute(): string
    {
        return route('tags.show', $this->slug);
    }

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

    public function incrementArticleCount(): void
    {
        $this->increment('article_count');
    }

    public function decrementArticleCount(): void
    {
        $this->decrement('article_count');
    }

    public static function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9\s-]/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return str_replace(' ', '-', $name);
    }
}