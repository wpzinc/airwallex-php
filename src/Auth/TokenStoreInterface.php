<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Auth;

/**
 * Persists access tokens between requests.
 *
 * Airwallex tokens last 30 minutes and should be reused rather than requested per call,
 * so in a web app back this with a shared cache (e.g. Laravel's Cache, Redis, APCu).
 */
interface TokenStoreInterface
{
    public function get(string $key): ?AccessToken;

    public function put(string $key, AccessToken $token): void;

    public function forget(string $key): void;
}
