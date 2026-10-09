<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests;

use WPZinc\Airwallex\AirwallexClient;
use WPZinc\Airwallex\Tests\Support\FakeHttpClient;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected FakeHttpClient $http;

    protected AirwallexClient $client;

    protected function setUp(): void
    {
        $this->http   = new FakeHttpClient();
        $this->client = $this->makeClient();
    }

    protected function makeClient(array $config = []): AirwallexClient
    {
        $client = new AirwallexClient($config + [
            'client_id'   => 'client_123',
            'api_key'     => 'key_456',
            'environment' => 'sandbox',
            'http_client' => $this->http,
        ]);
        $client->getRequestor()->setSleeper(static function (float $s): void {
        });

        return $client;
    }
}
