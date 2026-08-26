<?php

namespace App\Services\AI;

use App\Models\RawArticle;
use App\Jobs\ProcessArticleWithAIJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EditorialRankerService
{
    protected string $apiKey;
    protected string $model;
    protected float $minThreshold = 7.0; // Minimum score to promote to full publication

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key') ?? env('OPENROUTER_API_KEY', '');
        // Use fast, cost-effective evaluation model
        $this->model = 'deepseek/deepseek-chat';
    }

    /**
     * Evaluate and rank a raw article.
     * If score >= 7.0, dispatches ProcessArticleWithAIJob.
     * If score < 7.0, marks as ignored.
     */
    public function evaluate(RawArticle $rawArticle): array
    {
        // 0. Human Editor Manual Seed (Highest Priority: Always promoted)
        if ($rawArticle->source_id === null) {
            Log::info("⭐ EditorialRanker: MANUAL SEED detected for RawArticle #{$rawArticle->id} ('{$rawArticle->title}'). Bypassing heuristics and promoting with Priority 10/10.");
            return $this->markPromoted($rawArticle, 10.0, 'Semilla Editorial Manual creada directamente por el editor en Filament.');
        }
        // 1. Fast heuristic pre-checks
        $title = $rawArticle->title ?? '';
        $summary = $rawArticle->summary ?? '';
        $content = strip_tags($rawArticle->content ?? '');

        // Immediate rejection for deals, coupons, or pure promo snippets
        if (preg_match('/coupon|promo code|discount deal|discount code|% off|save \$|\bdeal of the day\b/i', $title)) {
            return $this->markIgnored($rawArticle, 1.0, 'Promotional / Coupon affiliate snippet with zero journalistic value.');
        }

        // Immediate rejection for empty snippets < 40 words
        if (str_word_count($content) < 40 && str_word_count($summary) < 20) {
            return $this->markIgnored($rawArticle, 2.0, 'Content body is too short or empty (under 40 words).');
        }

        // 2. Call AI Evaluation
        try {
            $prompt = <<<PROMPT
You are the Chief Technology Editor at Glodaxia Tech News.
Evaluate the following raw news item and decide if it deserves to be published as a major investigative tech article (>700 words).

NEWS ITEM:
- Title: "{$title}"
- Source: "{$rawArticle->source?->name}"
- Summary: "{$summary}"
- Body snippet: "{$this->sanitizeSnippet($content)}"

EVALUATION CRITERIA:
1. Tech Impact (1-10): Hardware, AI models, cybersecurity zero-days, infrastructure, cloud, developer tools.
2. Search Intent (1-10): Will tech professionals, engineers, and readers search for this today?
3. Editorial Substance (1-10): Is there real news value, or is it filler, shopping advice, a minor gamepad color, or routine changelog?

OUTPUT FORMAT:
Respond ONLY with a valid JSON object:
{
    "impact_score": <number 1-10>,
    "search_intent_score": <number 1-10>,
    "overall_score": <number 1.0-10.0>,
    "decision": "<promote|ignore>",
    "reason": "<One clear sentence explaining the decision>"
}

Note: "promote" requires overall_score >= 7.0. Otherwise "ignore".
PROMPT;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer'   => 'https://glodaxia.com',
                'X-Title'        => 'Glodaxia Editorial Ranker',
                'Content-Type'   => 'application/json',
            ])->timeout(30)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.1,
                'response_format' => ['type' => 'json_object'],
            ]);

            if ($response->successful()) {
                $rawJson = $response->json('choices.0.message.content');
                $data = json_decode($rawJson, true);

                if (isset($data['overall_score'])) {
                    $score = (float)$data['overall_score'];
                    $decision = $data['decision'] ?? ($score >= $this->minThreshold ? 'promote' : 'ignore');
                    $reason = $data['reason'] ?? 'Evaluated by AI editorial ranker.';

                    if ($decision === 'promote' && $score >= $this->minThreshold) {
                        return $this->markPromoted($rawArticle, $score, $reason);
                    } else {
                        return $this->markIgnored($rawArticle, $score, $reason);
                    }
                }
            }

            // Fallback if AI call failed
            Log::warning("EditorialRanker: Fallback evaluation for RawArticle {$rawArticle->id}");
            return $this->markPromoted($rawArticle, 7.0, 'Promoted by fallback default.');
        } catch (\Throwable $e) {
            Log::error("EditorialRanker Exception for RawArticle {$rawArticle->id}: " . $e->getMessage());
            // In case of network error, do not drop critical news; promote with caution
            return $this->markPromoted($rawArticle, 7.0, 'Promoted due to ranker exception: ' . $e->getMessage());
        }
    }

    protected function markPromoted(RawArticle $rawArticle, float $score, string $reason): array
    {
        $metadata = $rawArticle->metadata ?? [];
        $metadata['curation'] = [
            'score'    => $score,
            'decision' => 'promote',
            'reason'   => $reason,
            'rated_at' => now()->toIso8601String(),
        ];

        $rawArticle->update([
            'metadata' => $metadata,
        ]);

        Log::info("⭐ EditorialRanker: PROMOTED RawArticle #{$rawArticle->id} (Score: {$score}/10) - '{$rawArticle->title}'. Reason: {$reason}");
        
        // Dispatch heavy drafting and image generation
        ProcessArticleWithAIJob::dispatch($rawArticle);

        return [
            'status'   => 'promoted',
            'score'    => $score,
            'reason'   => $reason,
        ];
    }

    protected function markIgnored(RawArticle $rawArticle, float $score, string $reason): array
    {
        $metadata = $rawArticle->metadata ?? [];
        $metadata['curation'] = [
            'score'    => $score,
            'decision' => 'ignore',
            'reason'   => $reason,
            'rated_at' => now()->toIso8601String(),
        ];

        $rawArticle->update([
            'status'   => 'ignored',
            'metadata' => $metadata,
        ]);

        Log::info("⚪ EditorialRanker: IGNORED RawArticle #{$rawArticle->id} (Score: {$score}/10) - '{$rawArticle->title}'. Reason: {$reason}");

        return [
            'status'   => 'ignored',
            'score'    => $score,
            'reason'   => $reason,
        ];
    }

    protected function sanitizeSnippet(string $text): string
    {
        $clean = preg_replace('/\s+/', ' ', $text);
        return mb_substr($clean, 0, 500);
    }
}