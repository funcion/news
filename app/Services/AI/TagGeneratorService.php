<?php

namespace App\Services\AI;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagGeneratorService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Generate tags from article content using AI.
     * Returns an array of normalized tag names.
     */
    public function generateTags(string $content): array
    {
        $prompt = "Extract up to 5 key technologies, concepts or entities from the following text. "
                . "Response must be a comma-separated list of tags, no markdown, no quotes, lowercase. "
                . "Focus on tech terms like 'artificial intelligence', 'openai', 'react', etc.\n\n"
                . "Text: " . Str::limit(strip_tags($content), 3000);

        try {
            $response = $this->ai->complete(
                [['role' => 'user', 'content' => $prompt]],
                OpenRouterService::MODEL_GEMINI_LATEST
            );

            if (!$response) {
                return [];
            }

            return $this->normalizeTags(explode(',', $response));

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating tags: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync extracted tags to an article.
     */
    public function syncTagsToArticle(\App\Models\Article $article, array $tagNames): void
    {
        if (empty($tagNames)) {
            return;
        }

        $tagIds = [];

        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => [
                        'en' => ucwords($name),
                        'es' => ucwords($name),
                    ],
                ] // Requires spatie translatable format
            );

            // Increment count safely elsewhere or here, wait, we can just use the DB
            // $article_tag table can just sync
            $tagIds[$tag->id] = ['relevance_score' => 100];
        }

        $article->tags()->syncWithoutDetaching($tagIds);

        // Update tag counts
        foreach ($tagIds as $id => $pivot) {
            $tag = Tag::find($id);
            if ($tag) {
                $tag->article_count = $tag->articles()->count();
                $tag->save();
            }
        }
    }

    private function normalizeTags(array $tags): array
    {
        return collect($tags)
            ->map(function ($tag) {
                $tag = strtolower(trim($tag));
                $tag = preg_replace('/[^a-z0-9\s-]/', '', $tag);
                $tag = preg_replace('/\s+/', ' ', $tag);
                return $tag;
            })
            ->filter(function ($tag) {
                return strlen($tag) >= 2 && strlen($tag) <= 50;
            })
            ->unique()
            ->take(5)
            ->values()
            ->toArray();
    }
}
