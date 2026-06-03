<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunComposerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes

    public function __construct()
    {
        //
    }

    public function handle()
    {
        Log::info('RunComposerJob: Starting composer update...');
        $logFile = '/app/public/composer_output.txt';
        
        $output = [];
        $status = 0;
        
        // Execute composer synchronously inside the queue worker
        exec('php /app/public/composer.phar update -d /app -W --prefer-source --no-interaction > ' . escapeshellarg($logFile) . ' 2>&1', $output, $status);
        
        Log::info('RunComposerJob: Composer update finished with status ' . $status);
    }
}
