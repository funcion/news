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
        $this->apiKey = env('OPENROUTER_API_KEY', '');
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
     * Get embeddings from OpenRouter.
     *
     * @param string $input
     * @param string $model
     * @return array|null The embedding vector (e.g. 1536 floats)
     */
    public function embeddings(string $input, string $model = 'openai/text-embedding-3-small'): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'X-Title' => 'Noticias Platform',
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
            ])
            ->timeout(60)
            ->post($this->baseUrl . '/embeddings', [
                'model' => $model,
                'input' => $input,
            ]);

            if ($response->failed()) {
                Log::error("OpenRouter Embeddings Error: " . $response->status() . " - " . $response->body());
                return null;
            }

            $data = $response->json();
            return $data['data'][0]['embedding'] ?? null;
        } catch (\Exception $e) {
            Log::error("OpenRouter Embeddings Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ═══════════════════════════════════════════════════════════════════
     *  MODELOS DISPONIBLES PARA EL PIPELINE DE REDACCIÓN
     * ═══════════════════════════════════════════════════════════════════
     *
     *  Para cambiar de modelo, edita MODEL_ACTIVE abajo.
     *  Copia el string del modelo que quieras usar.
     *
     * ═══════════════════════════════════════════════════════════════════
     */

    // --- Google ---
    // public const MODEL_ACTIVE = 'google/gemini-2.5-flash';    // Rápido, barato (DEFAULT)
    // public const MODEL_ACTIVE = 'google/gemini-2.5-pro';      // Más inteligente

    // --- Xiaomi ---
    // public const MODEL_ACTIVE = 'xiaomi/mimo-v2.5-pro';       // Xiaomi MiMo v2.5 Pro

    // --- Qwen (Alibaba) ---
    // public const MODEL_ACTIVE = 'qwen/qwen3-235b-a22b';      // Qwen3 235B

    // --- DeepSeek ---
    // public const MODEL_ACTIVE = 'deepseek/deepseek-chat-v4-0324';  // DeepSeek V4 Flash
    // public const MODEL_ACTIVE = 'deepseek/deepseek-r1-0528';       // DeepSeek V4 Pro / R1

    // ═══════════════════════════════════════════════════════════════════
    //  >>> MODELO ACTIVO (descomenta otro arriba, comenta éste) <<<
    // ═══════════════════════════════════════════════════════════════════
    public const MODEL_ACTIVE = 'google/gemini-2.5-flash';
}
