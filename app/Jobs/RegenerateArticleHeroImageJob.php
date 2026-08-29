<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\AI\SiliconFlowImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RegenerateArticleHeroImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public Article $article, public ?string $customPrompt = null)
    {
    }

    public function handle(SiliconFlowImageService $imageService): void
    {
        try {
            $success = $imageService->regenerateHeroForArticle($this->article, $this->customPrompt);
            if ($success) {
                Log::info("Hero image regenerated successfully for Article #{$this->article->id}");
            } else {
                Log::warning("Hero image regeneration returned false for Article #{$this->article->id}");
            }
        } catch (\Throwable $e) {
            Log::error("Failed to regenerate hero image for Article #{$this->article->id}: " . $e->getMessage());
            throw $e;
        } finally {
            Cache::forget("article_image_processing_{$this->article->id}");
        }
    }
}