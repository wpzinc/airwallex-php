<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Auth;

/**
 * Default token store, scoped to the current PHP process.
 */
final class InMemoryTokenStore implements TokenStoreInterface
{
    /** @var array<string, AccessToken> */
    private array $tokens = [];

    public function get(string $key): ?AccessToken
    {
        return $this->tokens[$key] ?? null;
    }

    public function put(string $key, AccessToken $token): void
    {
        $this->tokens[$key] = $token;
    }

    public function forget(string $key): void
    {
        unset($this->tokens[$key]);
    }
}
