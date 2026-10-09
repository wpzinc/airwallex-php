<?php

declare(strict_types=1);

namespace WPZinc\Airwallex;

use WPZinc\Airwallex\Auth\InMemoryTokenStore;
use WPZinc\Airwallex\Auth\TokenStoreInterface;
use WPZinc\Airwallex\Exception\InvalidArgumentException;
use WPZinc\Airwallex\HttpClient\ClientInterface;
use WPZinc\Airwallex\HttpClient\CurlClient;
use WPZinc\Airwallex\Service\CoreServiceFactory;

/**
 * Airwallex API client.
 *
 * ```php
 * $airwallex = new AirwallexClient([
 *     'client_id'   => '...',
 *     'api_key'     => '...',
 *     'environment' => 'sandbox',
 * ]);
 *
 * $intent = $airwallex->paymentIntents->create([...]);
 * ```
 *
 * Config options:
 * - client_id, api_key        Credentials, exchanged for an access token automatically.
 * - access_token              Use a token you already have (e.g. Partner API OAuth) instead.
 * - environment               "production" (default) or "sandbox".
 * - api_version               x-api-version header. Defaults to API_VERSION; null for your account default.
 * - login_as                  x-login-as account ID, for scoped keys linked to several accounts.
 * - on_behalf_of              Default x-on-behalf-of connected account ID.
 * - max_network_retries       Retries for connection errors, 429 and 5xx (default 2).
 * - timeout                   Request timeout in seconds (default 80).
 * - auto_request_id           Add a request_id UUID where the endpoint needs one (default true).
 * - http_client               A ClientInterface implementation (default CurlClient).
 * - token_store               A TokenStoreInterface implementation (default in-memory).
 * - base_url, files_base_url  Override API hosts.
 */
class AirwallexClient extends CoreServiceFactory
{
    public const VERSION = '0.1.0';

    /** API version this SDK release was built against. */
    public const API_VERSION = '2026-08-21';

    private readonly ClientConfig $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $httpClient = $config['http_client'] ?? new CurlClient();
        $tokenStore = $config['token_store'] ?? new InMemoryTokenStore();

        if (! $httpClient instanceof ClientInterface) {
            throw new InvalidArgumentException('http_client must implement ' . ClientInterface::class);
        }
        if (! $tokenStore instanceof TokenStoreInterface) {
            throw new InvalidArgumentException('token_store must implement ' . TokenStoreInterface::class);
        }

        $this->config = new ClientConfig($config);

        parent::__construct(new ApiRequestor($this->config, $httpClient, $tokenStore));
    }

    public function getConfig(): ClientConfig
    {
        return $this->config;
    }

    public function getRequestor(): ApiRequestor
    {
        return $this->requestor;
    }

    /**
     * Send a request to any endpoint, e.g. one this SDK doesn't wrap yet.
     *
     * @param array<string, mixed> $params Query params for GET, JSON body otherwise.
     * @param array<string, mixed> $opts   Request options (on_behalf_of, api_version, headers, timeout).
     */
    public function rawRequest(string $method, string $path, array $params = [], array $opts = [], bool $requestId = false): AirwallexObject
    {
        [$json, $response] = $this->requestor->request($method, $path, $params, $opts, $requestId);

        if (is_array($json) && array_is_list($json)) {
            return Collection::fromResponse($json, AirwallexObject::class, $params, null, $response);
        }

        return AirwallexObject::constructFrom(is_array($json) ? $json : [], $response);
    }
}
