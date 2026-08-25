<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

class ModelRouterService
{
    protected OpenRouterService $openRouter;

    public function __construct(OpenRouterService $openRouter)
    {
        $this->openRouter = $openRouter;
    }

    /**
     * Get the list of active models configured in the pool.
     *
     * @return array<string>
     */
    public function getPool(): array
    {
        $pool = config('ai_models.pool', []);
        $default = config('ai_models.default', 'deepseek/deepseek-v4-flash-0731');

        if (empty($pool)) {
            return [$default];
        }

        // Ensure default model is first if present
        if (in_array($default, $pool)) {
            $pool = array_unique(array_merge([$default], $pool));
        }

        return array_values($pool);
    }

    /**
     * Select a random model from the pool, optionally excluding already failed models.
     *
     * @param array<string> $exclude
     * @return string|null
     */
    public function selectModel(array $exclude = []): ?string
    {
        $available = array_values(array_diff($this->getPool(), $exclude));

        if (empty($available)) {
            return null;
        }

        return $available[array_rand($available)];
    }

    /**
     * Execute a chat completion with dynamic model selection and automatic chain failover.
     *
     * @param array $messages
     * @param array $options
     * @param string|null $preferredModel
     * @return array|null ['content' => string, 'model_used' => string, 'attempts' => array]
     */
    public function completeWithFailover(array $messages, array $options = [], ?string $preferredModel = null): ?array
    {
        $pool = $this->getPool();
        $tried = [];
        $attempts = [];

        // Determine default safe token limit (default 10,000 for full bilingual articles)
        $defaultMaxTokens = config('ai_models.max_tokens', 10000);
        $mergedOptions = array_merge([
            'max_tokens'  => $defaultMaxTokens,
            'temperature' => config('ai_models.temperature', 0.7),
        ], $options);

        // If a preferred model is requested and present in the pool, try it first
        $nextModel = ($preferredModel && in_array($preferredModel, $pool)) 
            ? $preferredModel 
            : $this->selectModel();

        while ($nextModel !== null) {
            $tried[] = $nextModel;
            Log::info("ModelRouter: Attempting completion with model [{$nextModel}] (max_tokens: {$mergedOptions['max_tokens']})");

            $startTime = microtime(true);
            $content = $this->openRouter->complete($messages, $nextModel, $mergedOptions);
            $elapsedMs = round((microtime(true) - $startTime) * 1000, 1);

            $attempts[] = [
                'model'      => $nextModel,
                'duration'   => $elapsedMs,
                'successful' => !empty($content),
            ];

            if (!empty($content)) {
                Log::info("ModelRouter: Completed successfully with [{$nextModel}] in {$elapsedMs}ms.");
                return [
                    'content'    => $content,
                    'model_used' => $nextModel,
                    'attempts'   => $attempts,
                ];
            }

            // If it failed, select next fallback model from remaining pool
            $fallbackModel = $this->selectModel($tried);
            if ($fallbackModel) {
                Log::warning("ModelRouter: Model [{$nextModel}] failed or returned empty. Failing over to [{$fallbackModel}]...");
            } else {
                Log::error("ModelRouter: All models in pool exhausted without success. Tried: " . implode(', ', $tried));
            }

            $nextModel = $fallbackModel;
        }

        return null;
    }

    /**
     * Fast classification helper with reduced token limits.
     *
     * @param array $messages
     * @param array $options
     * @return array|null
     */
    public function classifyWithFailover(array $messages, array $options = []): ?array
    {
        $classMaxTokens = config('ai_models.classification_max_tokens', 1500);
        $options['max_tokens'] = $classMaxTokens;

        return $this->completeWithFailover($messages, $options);
    }
}