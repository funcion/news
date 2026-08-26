<?php

namespace App\Observers;

use App\Models\RawArticle;
use App\Jobs\CurateRawArticleJob;
use Illuminate\Support\Facades\Log;

class RawArticleObserver
{
    /**
     * Handle the RawArticle "created" event.
     * Automatically dispatches the AI Editorial Ranker to curate and score newly ingested raw news.
     */
    public function created(RawArticle $rawArticle): void
    {
        if ($rawArticle->status === 'pending') {
            Log::info("RawArticle created: ID {$rawArticle->id}. Dispatching CurateRawArticleJob for AI evaluation.");
            CurateRawArticleJob::dispatch($rawArticle);
        }
    }
}