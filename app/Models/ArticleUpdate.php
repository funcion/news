<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleUpdate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'article_id',
        'title',
        'content',
        'summary',
        'source_url',
        'published_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the article that owns the update.
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Scope a query to only include recent updates.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    /**
     * Get the formatted update content.
     */
    public function getFormattedContentAttribute(): string
    {
        $content = "<div class='article-update'>";
        $content .= "<h4>📰 Actualización: {$this->title}</h4>";
        $content .= "<p class='update-time'><small>Actualizado el {$this->published_at->format('d/m/Y H:i')}</small></p>";
        $content .= "<div class='update-content'>{$this->content}</div>";
        
        if ($this->source_url) {
            $content .= "<p class='update-source'><small>Fuente: <a href='{$this->source_url}' target='_blank' rel='nofollow'>{$this->source_url}</a></small></p>";
        }
        
        $content .= "</div>";
        
        return $content;
    }

    /**
     * Get the update summary for notifications.
     */
    public function getNotificationSummaryAttribute(): string
    {
        $summary = $this->summary ?: substr(strip_tags($this->content), 0, 150) . '...';
        return "📰 {$this->title}: {$summary}";
    }

    /**
     * Check if this update adds significant new information.
     */
    public function isSignificantUpdate(): bool
    {
        // Check if update contains important keywords or has substantial content
        $importantKeywords = ['nuevo', 'importante', 'crítico', 'urgente', 'actualización', 'cambio', 'anuncio'];
        $content = strtolower($this->content);
        
        foreach ($importantKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }
        
        // Check content length
        $wordCount = str_word_count(strip_tags($this->content));
        return $wordCount > 50;
    }
}