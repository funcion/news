<?php

namespace App\Services\AI;

use App\Models\Article;
use App\Models\ArticleUpdate;
use Illuminate\Support\Facades\Log;

class DuplicateCheckerService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Check for duplicates using Level 2 (Text similarity) and Level 3 (Semantic pgvector similarity)
     * Returns true if a duplicate is found (and handled), false if it's a completely new article.
     */
    public function checkAndHandleDuplicate(string $title, string $content, string $url, int $rawArticleId): bool
    {
        Log::info("Running Anti-Duplicate Level 2 & 3 for title: {$title}");

        // Nivel 2: TF-IDF o similitud rápida basada en texto (Simular usando subcadenas)
        // Check for exact title strings in DB to quickly discard exact matches 
        // that somehow bypassed Level 1 (which checks RawArticle hash).
        $similarByTitle = Article::whereRaw("title->>'en' ILIKE ?", ["%{$title}%"])
                                 ->orWhereRaw("title->>'es' ILIKE ?", ["%{$title}%"])
                                 ->first();

        if ($similarByTitle) {
            Log::info("Level 2 Duplicate found by title similarity: Article ID {$similarByTitle->id}");
            $this->createUpdateEntry($similarByTitle, $url, $rawArticleId);
            return true;
        }

        // Nivel 3: IA Semántica con pgvector
        // Convert input text (Title + snippet) to an embedding
        $textToEmbed = substr($title . ". " . strip_tags($content), 0, 1000);
        $embedding = $this->ai->embeddings($textToEmbed);

        if (!$embedding) {
            Log::warning("Could not generate embedding for duplicate checking. Allowing content.");
            return false;
        }

        // We assume pgvector is installed. We must encode the array for Postgres `[1.0, 2.0,...]`
        $vectorString = '[' . implode(',', $embedding) . ']';

        // Find similar articles using cosine distance (<=>). Threshold 0.15 indicates high similarity
        $similarArticle = Article::select('id', 'title', 'embedding')
            ->whereNotNull('embedding')
            ->orderByRaw("embedding <=> ?::vector", [$vectorString])
            ->first();

        if ($similarArticle) {
            // Check the actual distance (we can't easily extract it from the orderByRaw without a select raw)
            // Let's explicitly calculate it
            $distanceResult = \Illuminate\Support\Facades\DB::selectOne(
                "SELECT (embedding <=> ?::vector) as distance FROM articles WHERE id = ?",
                [$vectorString, $similarArticle->id]
            );

            $distance = $distanceResult->distance ?? 1.0;
            
            Log::info("Level 3 Similar article checked: {$similarArticle->id} with distance {$distance}");

            if ($distance < 0.15) {
                Log::info("Level 3 Duplicate found! Distance: {$distance} < 0.15");
                $this->createUpdateEntry($similarArticle, $url, $rawArticleId);
                return true;
            }
        }

        return false;
    }

    /**
     * Attach this new raw source as an update to an existing article.
     */
    protected function createUpdateEntry(Article $article, string $url, int $rawArticleId): void
    {
        // Instead of processing a whole new article, we create an entry 
        // and optionally notify indexNow about the 'updated_at' bump.
        ArticleUpdate::create([
            'article_id' => $article->id,
            'raw_article_id' => $rawArticleId,
            'url' => $url,
            'added_at' => now()
        ]);

        $article->touch(); // Bump updated_at
        
        Log::info("Added as an update to Article ID {$article->id}. updated_at bumped.");
    }

    /**
     * Store the embedding on the newly created Article for future comparisons.
     */
    public function generateAndStoreEmbedding(Article $article, string $content): void
    {
        $textToEmbed = substr($article->getTranslation('title', 'en') . ". " . strip_tags($content), 0, 1000);
        $embedding = $this->ai->embeddings($textToEmbed);

        if ($embedding) {
            $article->update(['embedding' => $embedding]);
            Log::info("Embedding stored for Article {$article->id}");
        }
    }
}
