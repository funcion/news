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
     *  Para cambiar de modelo, comenta/descomenta la línea
     *  MODEL_ACTIVE en la sección de abajo.
     *
     *  Solo UNA línea MODEL_ACTIVE debe estar activa.
     * ═══════════════════════════════════════════════════════════════════
     */

    // --- Google ---
    public const MODEL_GEMINI_25_FLASH  = 'google/gemini-2.5-flash';
    public const MODEL_GEMINI_25_PRO    = 'google/gemini-2.5-pro';

    // --- Xiaomi ---
    public const MODEL_XIAOMI_V25_PRO   = 'xiaomi/mimo-v2.5-pro';

    // --- Qwen (Alibaba) ---
    public const MODEL_QWEN3_PLUS       = 'qwen/qwen3-235b-a22b';

    // --- DeepSeek ---
    public const MODEL_DEEPSEEK_V4_FLASH = 'deepseek/deepseek-chat-v4-0324';
    public const MODEL_DEEPSEEK_V4_PRO   = 'deepseek/deepseek-r1-0528';

    // --- OpenAI ---
    public const MODEL_GPT_41           = 'openai/gpt-4.1';
    public const MODEL_GPT_41_MINI      = 'openai/gpt-4.1-mini';
    public const MODEL_O3               = 'openai/o3';

    // --- Anthropic ---
    public const MODEL_CLAUDE_SONNET4   = 'anthropic/claude-sonnet-4';
    public const MODEL_CLAUDE_HAIKU     = 'anthropic/claude-3.5-haiku';

    // ═══════════════════════════════════════════════════════════════════
    //  >>> MODELO ACTIVO — Comenta/descomenta para cambiar <<<
    // ═══════════════════════════════════════════════════════════════════

    public const MODEL_ACTIVE =
        // self::MODEL_GEMINI_25_FLASH     // Google Gemini 2.5 Flash (rápido, barato)
        // self::MODEL_GEMINI_25_PRO        // Google Gemini 2.5 Pro (más inteligente)
        // self::MODEL_XIAOMI_V25_PRO        // Xiaomi MiMo v2.5 Pro
        // self::MODEL_QWEN3_PLUS            // Qwen3 235B (Alibaba)
        // self::MODEL_DEEPSEEK_V4_FLASH     // DeepSeek V4 Flash
        // self::MODEL_DEEPSEEK_V4_PRO       // DeepSeek V4 Pro / R1
        // self::MODEL_GPT_41                // OpenAI GPT-4.1
        // self::MODEL_GPT_41_MINI           // OpenAI GPT-4.1 Mini
        // self::MODEL_O3                    // OpenAI o3
        // self::MODEL_CLAUDE_SONNET4        // Anthropic Claude Sonnet 4
        // self::MODEL_CLAUDE_HAIKU          // Anthropic Claude 3.5 Haiku
        self::MODEL_GEMINI_25_FLASH;       // ← DEFAULT (descomenta otro arriba y comenta esta)

    /**
     * Alias legacy para compatibilidad con código existente.
     */
    public const MODEL_GEMINI_LATEST = self::MODEL_ACTIVE;
}
