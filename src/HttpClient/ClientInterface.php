<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\HttpClient;

use WPZinc\Airwallex\Exception\ApiConnectionException;

/**
 * Transport used by the SDK to send HTTP requests.
 *
 * Implement this to use your own HTTP stack (e.g. Guzzle, Laravel's Http client).
 */
interface ClientInterface
{
    /**
     * Send a request.
     *
     * @param string                    $method  Uppercase HTTP method.
     * @param string                    $url     Absolute URL, including any query string.
     * @param array<string, string>     $headers Header name => value.
     * @param string|array<string, mixed>|null $body JSON string, multipart fields (array) or null.
     * @param int                       $timeout Timeout in seconds.
     *
     * @throws ApiConnectionException When the request could not be completed.
     */
    public function request(string $method, string $url, array $headers, string|array|null $body, int $timeout): Response;
}
