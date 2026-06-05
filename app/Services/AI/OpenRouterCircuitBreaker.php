<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Circuit Breaker for OpenRouter API.
 *
 * When a 401 is detected, the circuit "opens" and ALL ProcessArticleWithAIJob
 * instances are released with a long delay instead of making useless API calls.
 * Auto-closes when the API key is tested successfully.
 */
class OpenRouterCircuitBreaker
{
    private const CACHE_KEY     = 'openrouter:circuit_open';
    private const FAILURE_COUNT = 'openrouter:401_count';
    private const THRESHOLD     = 3;        // Open circuit after 3 consecutive 401s
    private const OPEN_TTL      = 900;      // Stay open for 15 minutes

    /**
     * Is the circuit currently open (API key invalid)?
     */
    public static function isOpen(): bool
    {
        return Cache::get(self::CACHE_KEY, false);
    }

    /**
     * Record a 401 failure. Opens the circuit after THRESHOLD consecutive failures.
     */
    public static function recordFailure(): void
    {
        $count = Cache::increment(self::FAILURE_COUNT);
        Cache::put(self::FAILURE_COUNT, $count, now()->addHour());

        if ($count >= self::THRESHOLD) {
            Cache::put(self::CACHE_KEY, true, now()->addSeconds(self::OPEN_TTL));
            Log::critical("🔴 OpenRouter Circuit Breaker OPENED after {$count} consecutive 401 errors. All AI processing paused for 15 minutes. Update OPENROUTER_API_KEY in .env!");
        }
    }

    /**
     * Record a successful API call — reset the circuit.
     */
    public static function recordSuccess(): void
    {
        if (Cache::get(self::CACHE_KEY, false)) {
            Log::info("🟢 OpenRouter Circuit Breaker CLOSED — API key is working again.");
        }
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::FAILURE_COUNT);
    }

    /**
     * Reset the circuit manually (e.g., after updating the API key).
     */
    public static function reset(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::FAILURE_COUNT);
        Log::info("OpenRouter Circuit Breaker manually reset.");
    }

    /**
     * Get the remaining seconds the circuit will stay open.
     */
    public static function remainingTtl(): int
    {
        if (!self::isOpen()) {
            return 0;
        }
        $ttl = Cache::store()->getRedis()->ttl(self::CACHE_KEY);
        return max(0, $ttl);
    }
}
