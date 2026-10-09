<?php

declare(strict_types=1);

namespace WPZinc\Airwallex;

use WPZinc\Airwallex\Exception\InvalidArgumentException;

/**
 * Validated client configuration.
 */
final class ClientConfig
{
    public const ENV_PRODUCTION = 'production';
    public const ENV_SANDBOX    = 'sandbox';

    private const BASE_URLS = [
        self::ENV_PRODUCTION => 'https://api.airwallex.com',
        self::ENV_SANDBOX    => 'https://api.sandbox.airwallex.com',
    ];

    private const FILES_BASE_URLS = [
        self::ENV_PRODUCTION => 'https://files.airwallex.com',
        self::ENV_SANDBOX    => 'https://files.sandbox.airwallex.com',
    ];

    private const KEYS = [
        'client_id', 'api_key', 'access_token', 'environment', 'base_url', 'files_base_url', 'api_version',
        'login_as', 'on_behalf_of', 'max_network_retries', 'timeout', 'auto_request_id', 'http_client', 'token_store',
    ];

    public readonly ?string $clientId;
    public readonly ?string $apiKey;
    public readonly ?string $accessToken;
    public readonly string $environment;
    public readonly string $baseUrl;
    public readonly string $filesBaseUrl;
    public readonly ?string $apiVersion;
    public readonly ?string $loginAs;
    public readonly ?string $onBehalfOf;
    public readonly int $maxNetworkRetries;
    public readonly int $timeout;
    public readonly bool $autoRequestId;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $unknown = array_diff(array_keys($config), self::KEYS);
        if ($unknown !== []) {
            throw new InvalidArgumentException('Unknown config option(s): ' . implode(', ', $unknown));
        }

        $this->clientId    = self::stringOrNull($config, 'client_id');
        $this->apiKey      = self::stringOrNull($config, 'api_key');
        $this->accessToken = self::stringOrNull($config, 'access_token');

        if ($this->accessToken === null && ($this->clientId === null || $this->apiKey === null)) {
            throw new InvalidArgumentException('Provide client_id and api_key, or an access_token');
        }

        $this->environment = (string) ($config['environment'] ?? self::ENV_PRODUCTION);
        if (! isset(self::BASE_URLS[$this->environment])) {
            throw new InvalidArgumentException(sprintf('environment must be "%s" or "%s"', self::ENV_PRODUCTION, self::ENV_SANDBOX));
        }

        $this->baseUrl           = self::stringOrNull($config, 'base_url') ?? self::BASE_URLS[$this->environment];
        $this->filesBaseUrl      = self::stringOrNull($config, 'files_base_url') ?? self::FILES_BASE_URLS[$this->environment];
        $this->apiVersion        = array_key_exists('api_version', $config) ? self::stringOrNull($config, 'api_version') : AirwallexClient::API_VERSION;
        $this->loginAs           = self::stringOrNull($config, 'login_as');
        $this->onBehalfOf        = self::stringOrNull($config, 'on_behalf_of');
        $this->maxNetworkRetries = max(0, (int) ($config['max_network_retries'] ?? 2));
        $this->timeout           = max(1, (int) ($config['timeout'] ?? 80));
        $this->autoRequestId     = (bool) ($config['auto_request_id'] ?? true);
    }

    /**
     * @param array<string, mixed> $config
     */
    private static function stringOrNull(array $config, string $key): ?string
    {
        $value = $config[$key] ?? null;

        return $value === null || $value === '' ? null : (string) $value;
    }
}
