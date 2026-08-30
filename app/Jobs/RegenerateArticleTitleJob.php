<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\AI\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
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
1. LANGUAGE PURITY (ZERO TOLERANCE): title_en MUST be 100% in English. title_es MUST be 100% in Spanish. Never output Spanish in title_en or English in title_es.
2. Length: Target between 70 and 115 characters. ABSOLUTE MAXIMUM is {$titleMaxChars} characters (STRICTLY between {$titleMinChars} and {$titleMaxChars} chars). NEVER exceed {$titleMaxChars} characters.
3. Word count: Minimum {$titleMinWords} words.
4. Structure: Standalone full sentence with subject, action, and key impact.
5. STRICT: NEVER truncate, never leave sentences unfinished, no clickbait.

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
                // Strict language verification
                $esMarkersInEn = preg_match_all('/\b(?:de|la|los|las|del|en|para|con|sobre|según|este|esta)\b/iu', $data['title_en']);
                $enMarkersInEn = preg_match_all('/\b(?:the|and|with|from|which|about|between|for|after)\b/iu', $data['title_en']);
                if ($esMarkersInEn >= 2 && $enMarkersInEn === 0) {
                    throw new \Exception("AI generated Spanish in title_en: {$data['title_en']}");
                }

                $enMarkersInEs = preg_match_all('/\b(?:the|and|with|from|which|about|between|for|after)\b/iu', $data['title_es']);
                $esMarkersInEs = preg_match_all('/\b(?:de|la|los|las|del|en|para|con|sobre|según|este|esta)\b/iu', $data['title_es']);
                if ($enMarkersInEs >= 2 && $esMarkersInEs === 0) {
                    throw new \Exception("AI generated English in title_es: {$data['title_es']}");
                }

                $titleEs = $this->sanitizeTitle($data['title_es'], $titleMinChars, $titleMaxChars);
                $titleEn = $this->sanitizeTitle($data['title_en'], $titleMinChars, $titleMaxChars);

                $this->article->setTranslation('title', 'es', $titleEs);
                $this->article->setTranslation('title', 'en', $titleEn);
                $this->article->save();
                Log::info("Regenerated title for Article #{$this->article->id}: {$titleEs}");
            } else {
                throw new \Exception("Invalid JSON returned: {$rawJson}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to regenerate title for Article #{$this->article->id}: " . $e->getMessage());
            throw $e;
        } finally {
            Cache::forget("article_title_processing_{$this->article->id}");
        }
    }

    protected function sanitizeTitle(string $title, int $minChars, int $maxChars): string
    {
        $title = trim(strip_tags($title));
        $title = trim($title, " \t\n\r\0\x0B\"'«»“”");

        if (mb_strlen($title) <= $maxChars) {
            return $title;
        }

        $sub = mb_substr($title, 0, $maxChars);
        $lastSpace = mb_strrpos($sub, ' ');
        if ($lastSpace !== false && $lastSpace >= $minChars) {
            $sub = mb_substr($sub, 0, $lastSpace);
        }
        $sub = preg_replace('/\s+(?:de|del|con|para|por|en|a|y|e|o|u|que|sobre|tras|the|and|or|for|with|in|on|at|by|of|to|from|as|an|a)$/iu', '', $sub);
        return rtrim($sub, " ,;:-–—/|\\");
    }
}
