<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OpenRouterService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('OPENROUTER_API_KEY');
        $this->baseUrl = env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');
    }

    /**
     * Send a completion request to OpenRouter.
     *
     * @param array $messages
     * @param string $model
     * @param array $options
     * @return string|null
     */
    public function complete(array $messages, string $model, array $options = []): ?string
    {
        try {
            Log::info("OpenRouter Request: Model={$model}");

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'X-Title' => 'Noticias Platform', // Hardcoded local name for safety
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'), // Recommended by OpenRouter
            ])
            ->timeout(60)
            ->post($this->baseUrl . '/chat/completions', array_merge([
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
            ], $options));

            if ($response->failed()) {
                Log::error("OpenRouter Error: " . $response->status() . " - " . $response->body());
                return null;
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                Log::error("OpenRouter Error: Respuesta vacía o mal formada", ['data' => $data]);
                return null;
            }

            Log::info("OpenRouter Response: Length=" . strlen($content));

            return $content;
        } catch (\Exception $e) {
            Log::error("OpenRouter Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Models recommended for the AI Pipeline.
     */
    public const MODEL_GEMINI_LATEST = 'google/gemini-2.5-flash';
}
