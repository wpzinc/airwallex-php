<?php

declare(strict_types=1);

namespace WPZinc\Airwallex;

use WPZinc\Airwallex\Auth\AccessToken;
use WPZinc\Airwallex\Auth\TokenStoreInterface;
use WPZinc\Airwallex\Exception\ApiConnectionException;
use WPZinc\Airwallex\Exception\ApiErrorException;
use WPZinc\Airwallex\Exception\AuthenticationException;
use WPZinc\Airwallex\Exception\InvalidArgumentException;
use WPZinc\Airwallex\HttpClient\ClientInterface;
use WPZinc\Airwallex\HttpClient\Response;
use WPZinc\Airwallex\Util\Util;

/**
 * Sends authenticated requests to the Airwallex API, handling tokens, retries and errors.
 *
 * @internal Use AirwallexClient and its services instead.
 */
class ApiRequestor
{
    public const LOGIN_PATH = '/api/v1/authentication/login';

    /** Max seconds to wait between retries. */
    private const MAX_RETRY_DELAY = 5.0;

    /** Request option keys accepted per call. */
    private const OPTION_KEYS = ['on_behalf_of', 'api_version', 'headers', 'timeout', 'base_url', 'login_as'];

    /** @var (\Closure(float): void) */
    private \Closure $sleeper;

    public function __construct(
        private readonly ClientConfig $config,
        private readonly ClientInterface $httpClient,
        private readonly TokenStoreInterface $tokenStore,
    ) {
        $this->sleeper = static function (float $seconds): void {
            usleep((int) ($seconds * 1_000_000));
        };
    }

    /**
     * Override how the SDK waits between retries (used in tests).
     *
     * @param \Closure(float): void $sleeper
     */
    public function setSleeper(\Closure $sleeper): void
    {
        $this->sleeper = $sleeper;
    }

    /**
     * Send a JSON API request and return the decoded body with the raw response.
     *
     * @param array<string, mixed> $params Query params for GET, JSON body otherwise.
     * @param array<string, mixed> $opts   Per-request options.
     *
     * @return array{0: mixed, 1: Response}
     */
    public function request(string $method, string $path, array $params = [], array $opts = [], bool $requestId = false): array
    {
        $method = strtoupper($method);
        $this->validateOptions($opts);

        if ($requestId && $this->config->autoRequestId && $method !== 'GET' && ! isset($params['request_id'])) {
            $params['request_id'] = Util::uuid();
        }

        $url  = rtrim((string) ($opts['base_url'] ?? $this->config->baseUrl), '/') . $path;
        $body = null;

        if ($method === 'GET' || $method === 'DELETE') {
            $query = Util::encodeQuery($params);
            if ($query !== '') {
                $url .= (str_contains($url, '?') ? '&' : '?') . $query;
            }
        } else {
            $body = json_encode($params === [] ? new \stdClass() : $params, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        }

        $headers = ['Content-Type' => 'application/json'];

        $canRetry = $method === 'GET' || isset($params['request_id']);
        $response = $this->send($method, $url, $headers, $body, $opts, $canRetry);

        return [$response->json(), $response];
    }

    /**
     * Send a multipart/form-data request (file uploads).
     *
     * @param array<string, mixed> $fields Form fields; use \CURLFile for files.
     * @param array<string, mixed> $opts
     *
     * @return array{0: mixed, 1: Response}
     */
    public function requestMultipart(string $path, array $fields, array $opts = []): array
    {
        $this->validateOptions($opts);

        $url      = rtrim((string) ($opts['base_url'] ?? $this->config->filesBaseUrl), '/') . $path;
        $response = $this->send('POST', $url, [], $fields, $opts, false);

        return [$response->json(), $response];
    }

    /**
     * Get a valid access token, logging in if needed.
     */
    public function getAccessToken(bool $forceRefresh = false, ?string $loginAs = null): string
    {
        if ($this->config->accessToken !== null) {
            return $this->config->accessToken;
        }

        $loginAs ??= $this->config->loginAs;
        $key       = $this->tokenKey($loginAs);

        if (! $forceRefresh) {
            $token = $this->tokenStore->get($key);
            if ($token !== null && $token->isValid()) {
                return $token->token;
            }
        }

        $token = $this->login($loginAs);
        $this->tokenStore->put($key, $token);

        return $token->token;
    }

    /**
     * Drop the cached access token so the next request logs in again.
     */
    public function forgetAccessToken(?string $loginAs = null): void
    {
        $this->tokenStore->forget($this->tokenKey($loginAs ?? $this->config->loginAs));
    }

    /**
     * Exchange the client ID and API key for an access token.
     */
    public function login(?string $loginAs = null): AccessToken
    {
        if ($this->config->clientId === null || $this->config->apiKey === null) {
            throw new InvalidArgumentException('client_id and api_key are required to obtain an access token');
        }

        $headers = [
            'Content-Type' => 'application/json',
            'x-client-id'  => $this->config->clientId,
            'x-api-key'    => $this->config->apiKey,
        ];
        if ($loginAs !== null) {
            $headers['x-login-as'] = $loginAs;
        }

        $response = $this->sendRaw('POST', rtrim($this->config->baseUrl, '/') . self::LOGIN_PATH, $headers, '{}', $this->config->timeout, true);
        if ($response->status >= 400) {
            throw ApiErrorException::factory($response);
        }

        $json = $response->json();
        if (! is_array($json) || ! is_string($json['token'] ?? null)) {
            throw new AuthenticationException('Airwallex login response did not contain a token', $response->status);
        }

        $expiresAt = false;
        if (is_string($json['expires_at'] ?? null)) {
            $expiresAt = strtotime($json['expires_at']);
        }

        // Tokens last 30 minutes; fall back to 25 if expires_at is missing or unparseable.
        return new AccessToken($json['token'], $expiresAt !== false ? $expiresAt : time() + 1500);
    }

    /**
     * @param array<string, string>            $headers
     * @param string|array<string, mixed>|null $body
     * @param array<string, mixed>             $opts
     */
    private function send(string $method, string $url, array $headers, string|array|null $body, array $opts, bool $canRetry): Response
    {
        $loginAs = isset($opts['login_as']) ? (string) $opts['login_as'] : null;
        $headers = $headers + $this->defaultHeaders($opts);
        $timeout = (int) ($opts['timeout'] ?? $this->config->timeout);

        $headers['Authorization'] = 'Bearer ' . $this->getAccessToken(false, $loginAs);
        $response                 = $this->sendRaw($method, $url, $headers, $body, $timeout, $canRetry);

        // The token may have been revoked or expired early, so log in again once.
        if ($response->status === 401 && $this->config->accessToken === null) {
            $headers['Authorization'] = 'Bearer ' . $this->getAccessToken(true, $loginAs);
            $response                 = $this->sendRaw($method, $url, $headers, $body, $timeout, $canRetry);
        }

        if ($response->status >= 400) {
            throw ApiErrorException::factory($response);
        }

        return $response;
    }

    /**
     * Send with retries on connection errors, 429 and 5xx.
     *
     * @param array<string, string>            $headers
     * @param string|array<string, mixed>|null $body
     */
    private function sendRaw(string $method, string $url, array $headers, string|array|null $body, int $timeout, bool $canRetry): Response
    {
        $attempt = 0;
        while (true) {
            try {
                $response = $this->httpClient->request($method, $url, $headers, $body, $timeout);
            } catch (ApiConnectionException $e) {
                if (! $canRetry || $attempt >= $this->config->maxNetworkRetries) {
                    throw $e;
                }
                ($this->sleeper)($this->retryDelay($attempt++, null));
                continue;
            }

            // 429s weren't processed, so they're always safe to retry.
            $retryable = $response->status === 429 || ($canRetry && in_array($response->status, [500, 502, 503, 504], true));
            if (! $retryable || $attempt >= $this->config->maxNetworkRetries) {
                return $response;
            }

            ($this->sleeper)($this->retryDelay($attempt++, $response->header('retry-after')));
        }
    }

    private function retryDelay(int $attempt, ?string $retryAfter): float
    {
        if ($retryAfter !== null && is_numeric($retryAfter)) {
            return min((float) $retryAfter, self::MAX_RETRY_DELAY);
        }

        // Exponential backoff with jitter: ~0.5s, 1s, 2s...
        $delay = 0.5 * (2 ** $attempt);

        return min($delay * (0.75 + mt_rand() / mt_getrandmax() * 0.5), self::MAX_RETRY_DELAY);
    }

    /**
     * @param array<string, mixed> $opts
     *
     * @return array<string, string>
     */
    private function defaultHeaders(array $opts): array
    {
        $headers = [
            'Accept'     => 'application/json',
            'User-Agent' => sprintf('wpzinc-airwallex-php/%s PHP/%s', AirwallexClient::VERSION, PHP_VERSION),
        ];

        $apiVersion = array_key_exists('api_version', $opts) ? $opts['api_version'] : $this->config->apiVersion;
        if ($apiVersion !== null && $apiVersion !== '') {
            $headers['x-api-version'] = (string) $apiVersion;
        }

        $onBehalfOf = array_key_exists('on_behalf_of', $opts) ? $opts['on_behalf_of'] : $this->config->onBehalfOf;
        if ($onBehalfOf !== null && $onBehalfOf !== '') {
            $headers['x-on-behalf-of'] = (string) $onBehalfOf;
        }

        foreach ((array) ($opts['headers'] ?? []) as $name => $value) {
            $headers[(string) $name] = (string) $value;
        }

        return $headers;
    }

    /**
     * @param array<string, mixed> $opts
     */
    private function validateOptions(array $opts): void
    {
        $unknown = array_diff(array_keys($opts), self::OPTION_KEYS);
        if ($unknown !== []) {
            throw new InvalidArgumentException(sprintf(
                'Unknown request option(s): %s. Allowed: %s. Pass API fields in $params instead.',
                implode(', ', $unknown),
                implode(', ', self::OPTION_KEYS)
            ));
        }
    }

    private function tokenKey(?string $loginAs): string
    {
        return 'airwallex_token_' . sha1($this->config->baseUrl . '|' . $this->config->clientId . '|' . ($loginAs ?? ''));
    }
}
