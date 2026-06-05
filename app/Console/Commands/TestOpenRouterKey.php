<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\AI\OpenRouterCircuitBreaker;

class TestOpenRouterKey extends Command
{
    protected $signature = 'test:openrouter';
    protected $description = 'Test if the OpenRouter API key is working';

    public function handle(): int
    {
        $key = config('openrouter.api_key');

        $this->info('Key from config: ' . (empty($key) ? 'EMPTY!' : substr($key, 0, 25) . '...'));
        $this->info('Key length: ' . strlen($key));

        if (empty($key)) {
            $this->error('API key is EMPTY! Check OPENROUTER_API_KEY in .env');
            return 1;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $key,
            'Content-Type'  => 'application/json',
        ])->timeout(15)->post('https://openrouter.ai/api/v1/chat/completions', [
            'model'    => 'google/gemini-2.5-flash',
            'messages' => [['role' => 'user', 'content' => 'Say OK']],
            'max_tokens' => 5,
        ]);

        $this->info('Status: ' . $response->status());
        $this->info('Body: ' . substr($response->body(), 0, 300));

        if ($response->successful()) {
            $this->info('✅ API key is WORKING!');
            OpenRouterCircuitBreaker::reset();
        } else {
            $this->error('❌ API key FAILED with status: ' . $response->status());
        }

        // Show circuit breaker status
        if (OpenRouterCircuitBreaker::isOpen()) {
            $this->warn('⚠️  Circuit Breaker is OPEN — AI processing is paused!');
            $this->warn('   Run this command again after fixing the key to auto-reset.');
        }

        return 0;
    }
}
