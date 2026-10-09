<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\HttpClient;

use WPZinc\Airwallex\Exception\ApiConnectionException;

/**
 * Default cURL transport.
 */
class CurlClient implements ClientInterface
{
    /**
     * @param array<int, mixed> $curlOptions Extra CURLOPT_* options applied to every request.
     */
    public function __construct(private array $curlOptions = [], private int $connectTimeout = 30)
    {
    }

    public function request(string $method, string $url, array $headers, string|array|null $body, int $timeout): Response
    {
        $ch = curl_init();

        $headerLines = [];
        foreach ($headers as $name => $value) {
            $headerLines[] = $name . ': ' . $value;
        }

        $responseHeaders = [];
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headerLines,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADERFUNCTION => static function ($ch, string $line) use (&$responseHeaders): int {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return strlen($line);
            },
        ];

        if ($body !== null) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        curl_setopt_array($ch, $this->curlOptions + $options);

        $result = curl_exec($ch);

        if ($result === false) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            throw new ApiConnectionException(sprintf('Could not connect to Airwallex (%s): [%d] %s', $url, $errno, $error), $errno);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        return new Response($status, $responseHeaders, (string) $result);
    }
}
