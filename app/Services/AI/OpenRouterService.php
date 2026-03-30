<?php

namespace App\Services\AI;

use OpenAI;
use OpenAI\Client;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected Client $client;

    public function __construct()
    {
        $apiKey = env('OPENROUTER_API_KEY');
        $baseUrl = env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1');

        $this->client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri($baseUrl)
            ->withHttpHeader('X-Title', config('app.name'))
            ->make();
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

            $response = $this->client->chat()->create(array_merge([
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
            ], $options));

            $content = $response->choices[0]->message->content;

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
    public const MODEL_GEMINI_3_FLASH = 'google/gemini-3-flash-preview';
}
