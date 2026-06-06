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
        // Auto-dispatch on creation has been disabled to prevent uncontrolled token consumption.
        // Raw articles are saved as pending and can be processed manually via Filament 
        // or automatically via the 'ai:process-auto-capped' artisan command.
        
        Log::info("RawArticle created: ID {$rawArticle->id} (saved as pending).");
    }
}
