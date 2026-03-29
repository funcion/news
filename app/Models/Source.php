<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Source extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'type',
        'category',
        'frequency',
        'last_fetched_at',
        'is_active',
        'score',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_fetched_at' => 'datetime',
        'is_active' => 'boolean',
        'score' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the raw articles for this source.
     */
    public function rawArticles()
    {
        return $this->hasMany(RawArticle::class);
    }

    /**
     * Scope a query to only include active sources.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include sources that need fetching.
     */
    public function scopeNeedsFetching($query)
    {
        return $query->active()->where(function ($q) {
            $q->whereNull('last_fetched_at')
              ->orWhere('last_fetched_at', '<', now()->subMinutes($this->frequency));
        });
    }

    /**
     * Update the source score based on reliability metrics.
     */
    public function updateScore(array $metrics): void
    {
        $score = 0;
        
        // Reliability: less failures = higher score
        if (isset($metrics['reliability'])) {
            $score += $metrics['reliability'] * 30;
        }
        
        // Freshness: more frequent updates = higher score
        if (isset($metrics['freshness'])) {
            $score += $metrics['freshness'] * 25;
        }
        
        // Originality: unique content = higher score
        if (isset($metrics['originality'])) {
            $score += $metrics['originality'] * 25;
        }
        
        // Quality: better content = higher score
        if (isset($metrics['quality'])) {
            $score += $metrics['quality'] * 20;
        }
        
        $this->score = min(100, max(0, $score));
        
        // Deactivate if score is too low
        if ($this->score < 30) {
            $this->is_active = false;
        }
        
        $this->save();
    }
}