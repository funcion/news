<?php

namespace App\Services\AI;

use App\Models\Tag;
use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TagGeneratorService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Generate concise, high-density tags using a closed-loop taxonomy catalog.
     * Returns an array of normalized tag names (max 3).
     */
    public function generateTags(string $content): array
    {
        // 1. Fetch the existing Master Tags Catalog from database
        $catalogTags = Tag::where('is_active', true)
            ->pluck('name')
            ->map(fn ($n) => is_array($n) ? ($n['en'] ?? reset($n)) : (json_decode($n, true)['en'] ?? $n))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Fallback default catalog if DB is empty
        if (empty($catalogTags)) {
            $catalogTags = [
                'Artificial Intelligence', 'Large Language Models', 'OpenAI', 'Google', 'Meta',
                'Microsoft', 'NVIDIA', 'Apple', 'Anthropic', 'AI Agents', 'Robotics & Automation',
                'Cybersecurity', 'Vulnerabilities & Exploits', 'Ransomware & Malware',
                'Data Privacy & Protection', 'Cloud Computing', 'Hardware & Semiconductors',
                'Software Engineering', 'DevOps & CI/CD', 'Open Source', 'Databases & Storage',
                'Science & Innovation', 'Startups & Venture Capital'
            ];
        }

        $catalogListString = implode(', ', array_slice($catalogTags, 0, 50));

        $prompt = "You are the Senior Lead Taxonomist for a premier tech and AI news publication.\n"
                . "Your goal is to classify the article below by selecting 2 to 3 tags from our Official Master Taxonomy Catalog:\n"
                . "OFFICIAL CATALOG: [" . $catalogListString . "]\n\n"
                . "STRICT TAXONOMY RULES:\n"
                . "1. You MUST prioritize selecting 2 to 3 tags directly from the OFFICIAL CATALOG.\n"
                . "2. You may ONLY propose a new tag if the article is centered around a specific major tech company (e.g. 'DeepSeek', 'Mistral AI', 'TSMC', 'Groq') not in the list.\n"
                . "3. DO NOT invent generic phrases or sentence tags.\n"
                . "4. Output ONLY a comma-separated list of the 2-3 tags in lowercase. No explanation, no quotes, no markdown.\n\n"
                . "Article Content:\n" . Str::limit(strip_tags($content), 3000);

        try {
            $response = $this->ai->complete(
                [['role' => 'user', 'content' => $prompt]],
                config('ai_models.default'),
                ['max_tokens' => 150]
            );

            if (!$response) {
                return $this->fallbackTagsFromContent($content, $catalogTags);
            }

            $extracted = $this->normalizeTags(explode(',', $response));

            return !empty($extracted) ? $extracted : $this->fallbackTagsFromContent($content, $catalogTags);

        } catch (\Exception $e) {
            Log::error('Error in TagGeneratorService: ' . $e->getMessage());
            return $this->fallbackTagsFromContent($content, $catalogTags);
        }
    }

    /**
     * Sync extracted tags to an article with a strict limit of 3 tags.
     */
    public function syncTagsToArticle(Article $article, array $tagNames): void
    {
        if (empty($tagNames)) {
            return;
        }

        $tagNames = array_slice($tagNames, 0, 3);
        $tagIds = [];

        foreach ($tagNames as $name) {
            $slug = Str::slug($name);
            if (empty($slug)) {
                continue;
            }

            // Check if tag exists by slug
            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => [
                        'en' => ucwords($name),
                        'es' => ucwords($name),
                    ],
                    'description' => [
                        'en' => "Latest news and analysis on " . ucwords($name),
                        'es' => "Últimas noticias y análisis sobre " . ucwords($name),
                    ],
                    'is_active' => true,
                ]
            );

            $tagIds[$tag->id] = ['relevance_score' => 100];
        }

        $article->tags()->sync($tagIds);

        // Update tag counts
        foreach (array_keys($tagIds) as $id) {
            $t = Tag::find($id);
            if ($t) {
                $t->article_count = $t->articles()->count();
                $t->saveQuietly();
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
                return strlen($tag) >= 2 && strlen($tag) <= 40;
            })
            ->unique()
            ->take(3)
            ->values()
            ->toArray();
    }

    private function fallbackTagsFromContent(string $content, array $catalog): array
    {
        $contentLower = strtolower($content);
        $matched = [];

        foreach ($catalog as $catTag) {
            $tagLower = strtolower($catTag);
            if (str_contains($contentLower, $tagLower)) {
                $matched[] = $tagLower;
                if (count($matched) >= 3) {
                    break;
                }
            }
        }

        return !empty($matched) ? $matched : ['artificial-intelligence'];
    }
}