<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
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
        'bio',
        'avatar_url',
        'type',
        'voice_style',
        'is_active',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the articles for the author.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Scope a query to only include active authors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include AI authors.
     */
    public function scopeAi($query)
    {
        return $query->where('type', 'ai');
    }

    /**
     * Scope a query to only include human authors.
     */
    public function scopeHuman($query)
    {
        return $query->where('type', 'human');
    }

    /**
     * Get the URL for the author.
     */
    public function getUrlAttribute(): string
    {
        return route('authors.show', $this->slug);
    }

    /**
     * Get the author's article count.
     */
    public function getArticleCountAttribute(): int
    {
        return $this->articles()->count();
    }

    /**
     * Get the author's most recent articles.
     */
    public function getRecentArticles($limit = 5)
    {
        return $this->articles()
            ->published()
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the author's most popular articles.
     */
    public function getPopularArticles($limit = 5)
    {
        return $this->articles()
            ->published()
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    /**
     * Generate a bio for an AI author.
     */
    public static function generateAiBio(string $name, string $voiceStyle): string
    {
        $bios = [
            'El Analista' => "{$name} es un analista especializado en tecnología e inteligencia artificial. Con un enfoque en datos duros y análisis técnico, proporciona insights profundos sobre las tendencias más recientes en IA y automatización.",
            'El Divulgador' => "{$name} se especializa en hacer accesibles conceptos complejos de inteligencia artificial. Con un estilo claro y pedagógico, ayuda a entender cómo la IA está transformando nuestro mundo.",
            'El Cronista' => "{$name} narra las historias humanas detrás de los avances tecnológicos. Con un enfoque narrativo, explora el impacto social y cultural de la inteligencia artificial.",
            'El Crítico' => "{$name} ofrece análisis críticos y opiniones fundamentadas sobre productos y decisiones en el mundo de la IA. Con un enfoque honesto y directo, evalúa el impacto real de las tecnologías emergentes.",
        ];

        return $bios[$voiceStyle] ?? "{$name} es un escritor especializado en inteligencia artificial y automatización, proporcionando análisis y perspectivas únicas sobre las últimas tendencias tecnológicas.";
    }

    /**
     * Create an AI author with generated bio.
     */
    public static function createAiAuthor(string $name, string $voiceStyle, array $attributes = []): self
    {
        $slug = str_slug($name);
        
        return self::create(array_merge([
            'name' => $name,
            'slug' => $slug,
            'type' => 'ai',
            'voice_style' => $voiceStyle,
            'bio' => self::generateAiBio($name, $voiceStyle),
            'is_active' => true,
            'avatar_url' => self::generateAvatarUrl($name),
        ], $attributes));
    }

    /**
     * Generate a placeholder avatar URL for an author.
     */
    public static function generateAvatarUrl(string $name): string
    {
        $initials = strtoupper(substr($name, 0, 2));
        $colors = ['4A90E2', '50E3C2', 'F5A623', '7ED321', 'BD10E0', 'FF6B6B'];
        $color = $colors[array_rand($colors)];
        
        return "https://ui-avatars.com/api/?name={$initials}&background={$color}&color=fff&size=256";
    }
}