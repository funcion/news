<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\AI\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RegenerateArticleTitleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public Article $article)
    {
    }

    public function handle(OpenRouterService $ai): void
    {
        $titleMinChars = (int) config('global.editorial.limits.title.min', 50);
        $titleMaxChars = (int) config('global.editorial.limits.title.max', 130);
        $titleMinWords = (int) config('global.editorial.limits.title.min_words', 7);

        $excerpt = $this->article->getTranslation('excerpt', 'es') ?: $this->article->getTranslation('excerpt', 'en') ?: '';
        $body = strip_tags($this->article->getTranslation('content', 'es') ?: $this->article->getTranslation('content', 'en') ?: '');
        $sampleBody = mb_substr($body, 0, 1200);
        $currentTitle = $this->article->getTranslation('title', 'es') ?: $this->article->getTranslation('title', 'en');

        $prompt = <<<PROMPT
You are a senior tech journalism headline editor for Glodaxia.
Based on the following article facts and context, craft a compelling, complete, and highly descriptive headline in both English and Spanish.

RULES:
1. Length: Each headline MUST be between {$titleMinChars} and {$titleMaxChars} characters (aim for 70-130 chars).
2. Word count: Minimum {$titleMinWords} words.
3. Structure: Standalone full sentence with subject, action, and key impact.
4. STRICT: NEVER truncate, never leave sentences unfinished, no clickbait.

CURRENT HEADLINE: {$currentTitle}
EXCERPT: {$excerpt}
BODY SAMPLE: {$sampleBody}

Return ONLY valid JSON (no markdown wrapper, no extra text):
{
    "title_es": "Titular completo en español...",
    "title_en": "Complete headline in English..."
}
PROMPT;

        try {
            $rawJson = $ai->complete([['role' => 'user', 'content' => $prompt]], config('ai_models.default'));
            $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawJson));
            $data = json_decode($cleanJson, true);

            if (!empty($data['title_es']) && !empty($data['title_en'])) {
                $this->article->setTranslation('title', 'es', trim($data['title_es']));
                $this->article->setTranslation('title', 'en', trim($data['title_en']));
                $this->article->save();
                Log::info("Regenerated title for Article #{$this->article->id}: {$data['title_es']}");
            } else {
                throw new \Exception("Invalid JSON returned: {$rawJson}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to regenerate title for Article #{$this->article->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
