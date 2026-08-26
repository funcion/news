<?php

namespace App\Jobs;

use App\Models\RawArticle;
use App\Services\AI\EditorialRankerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CurateRawArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    public function __construct(
        public RawArticle $rawArticle
    ) {}

    public function handle(EditorialRankerService $ranker): void
    {
        // Fail-Fast Guard: Refresh from DB to verify if user changed status to 'ignored'
        $this->rawArticle->refresh();
        if (!$this->rawArticle->exists || $this->rawArticle->status !== 'pending') {
            Log::info("🛑 CurateRawArticleJob: Skipping #{$this->rawArticle->id} because current DB status is '{$this->rawArticle->status}'.");
            return;
        }

        Log::info("CurateRawArticleJob: Evaluating RawArticle #{$this->rawArticle->id} - '{$this->rawArticle->title}'");
        $ranker->evaluate($this->rawArticle);
    }
}