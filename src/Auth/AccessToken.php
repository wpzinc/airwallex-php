<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Auth;

/**
 * Bearer token returned by POST /api/v1/authentication/login.
 */
final class AccessToken
{
    public function __construct(
        public readonly string $token,
        public readonly int $expiresAt,
    ) {
    }

    /**
     * Whether the token is still usable, allowing a safety margin before expiry.
     */
    public function isValid(int $leewaySeconds = 60, ?int $now = null): bool
    {
        return $this->expiresAt - $leewaySeconds > ($now ?? time());
    }

    /**
     * @return array{token: string, expires_at: int}
     */
    public function toArray(): array
    {
        return ['token' => $this->token, 'expires_at' => $this->expiresAt];
    }

    /**
     * @param array{token: string, expires_at: int} $data
     */
    public static function fromArray(array $data): self
    {
        return new self((string) $data['token'], (int) $data['expires_at']);
    }
}
