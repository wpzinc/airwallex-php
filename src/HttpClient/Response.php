<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\HttpClient;

/**
 * Raw HTTP response.
 */
final class Response
{
    /** @var array<string, string> Lowercased header name => value. */
    public readonly array $headers;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly int $status,
        array $headers,
        public readonly string $body,
    ) {
        $this->headers = array_change_key_case($headers, CASE_LOWER);
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /**
     * Decoded JSON body, or null if the body is empty or not JSON.
     */
    public function json(): mixed
    {
        if ($this->body === '') {
            return null;
        }

        try {
            return json_decode($this->body, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (\JsonException) {
            return null;
        }
    }
}
