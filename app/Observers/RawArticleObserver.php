<?php

namespace App\Observers;

use App\Models\RawArticle;
use App\Jobs\ProcessArticleWithAIJob;
use Illuminate\Support\Facades\Log;

class RawArticleObserver
{
    /**
     * Handle the RawArticle "created" event.
     * Automatically dispatches AI processing for all newly ingested raw articles.
     */
    public function created(RawArticle $rawArticle): void
    {
        if ($rawArticle->status === 'pending') {
            Log::info("RawArticle created: ID {$rawArticle->id}. Auto-dispatching ProcessArticleWithAIJob.");
            ProcessArticleWithAIJob::dispatch($rawArticle);
        }
    }
}