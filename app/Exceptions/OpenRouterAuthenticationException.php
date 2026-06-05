<?php

namespace App\Exceptions;

/**
 * Thrown when OpenRouter returns a 401 authentication error.
 * This is a permanent failure (not transient) — retrying won't help
 * until the API key is corrected.
 */
class OpenRouterAuthenticationException extends \RuntimeException
{
    public function __construct(
        string $message = 'OpenRouter authentication failed',
        protected ?int $statusCode = 401,
        protected ?string $responseBody = null,
    ) {
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }
}
