<?php

namespace App\Observers;

use App\Models\RawArticle;
use App\Jobs\ProcessArticleWithAIJob;
use Illuminate\Support\Facades\Log;

class RawArticleObserver
{
    /**
     * Handle the RawArticle "created" event.
     */
    public function created(RawArticle $rawArticle): void
    {
        // Only trigger if status is pending (default)
        if ($rawArticle->status === 'pending') {
            Log::info("Auto-Dispatching AI Job for RawArticle: {$rawArticle->id}");
            ProcessArticleWithAIJob::dispatch($rawArticle);
        }
    }
}
