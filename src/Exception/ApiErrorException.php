<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Exception;

use WPZinc\Airwallex\HttpClient\Response;

/**
 * Base class for errors returned by the Airwallex API.
 */
class ApiErrorException extends \RuntimeException implements ExceptionInterface
{
    protected ?Response $response = null;

    /** Airwallex error code, e.g. "invalid_argument". */
    protected ?string $airwallexCode = null;

    /** Request parameter that caused the error, if any. */
    protected ?string $source = null;

    /** @var array<string, mixed>|null */
    protected ?array $details = null;

    /**
     * Build the exception matching the response's HTTP status.
     */
    public static function factory(Response $response): self
    {
        $class = match (true) {
            $response->status === 400 => InvalidRequestException::class,
            $response->status === 401 => AuthenticationException::class,
            $response->status === 403 => PermissionException::class,
            $response->status === 404 => NotFoundException::class,
            $response->status === 409 => ConflictException::class,
            $response->status === 429 => RateLimitException::class,
            $response->status >= 500  => ServerException::class,
            default                   => UnknownApiErrorException::class,
        };

        $json = $response->json();
        $json = is_array($json) ? $json : [];

        // Most endpoints use {code, message}; some return RFC 7807 {title, detail}
        $message = null;
        foreach (['message', 'detail', 'title'] as $key) {
            if (is_string($json[$key] ?? null) && $json[$key] !== '') {
                $message = $json[$key];
                break;
            }
        }
        $message ??= sprintf('Airwallex API request failed with HTTP %d', $response->status);

        // Airwallex returns 401 for missing scopes as well as bad tokens
        if ($response->status === 401 && stripos($message, 'permission') !== false) {
            $class = PermissionException::class;
        }

        if ($response->status === 400 && ($json['code'] ?? null) === 'duplicate_request') {
            $class = IdempotencyException::class;
        }

        $e                = new $class($message, $response->status);
        $e->response      = $response;
        $e->airwallexCode = isset($json['code']) ? (string) $json['code'] : null;
        $e->source        = isset($json['source']) ? (string) $json['source'] : null;
        $e->details       = isset($json['details']) && is_array($json['details']) ? $json['details'] : null;

        return $e;
    }

    public function getHttpStatus(): ?int
    {
        return $this->response?->status;
    }

    /**
     * @return array<string, string>
     */
    public function getHttpHeaders(): array
    {
        return $this->response?->headers ?? [];
    }

    public function getHttpBody(): ?string
    {
        return $this->response?->body;
    }

    public function getJsonBody(): mixed
    {
        return $this->response?->json();
    }

    public function getAirwallexCode(): ?string
    {
        return $this->airwallexCode;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getDetails(): ?array
    {
        return $this->details;
    }

    /**
     * Field-level validation errors from details.errors, e.g. [['code' => '001', 'source' => 'entity_type']].
     *
     * @return list<array<string, mixed>>
     */
    public function getErrors(): array
    {
        $errors = $this->details['errors'] ?? [];

        return is_array($errors) ? array_values(array_filter($errors, 'is_array')) : [];
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
