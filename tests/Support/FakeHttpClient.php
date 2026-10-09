<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests\Support;

use WPZinc\Airwallex\Exception\ApiConnectionException;
use WPZinc\Airwallex\HttpClient\ClientInterface;
use WPZinc\Airwallex\HttpClient\Response;

/**
 * Records requests and replays queued responses. Login requests are answered automatically.
 */
final class FakeHttpClient implements ClientInterface
{
    /** @var list<array{method: string, url: string, headers: array<string, string>, body: string|array|null}> */
    public array $requests = [];

    /** @var list<Response|ApiConnectionException> */
    private array $queue = [];

    public int $logins = 0;

    public string $token = 'tok_1';

    public string $expiresAt = '+30 minutes';

    public function queue(int $status, mixed $json = [], array $headers = []): self
    {
        $this->queue[] = new Response($status, $headers, is_string($json) ? $json : (string) json_encode($json));

        return $this;
    }

    public function queueConnectionError(): self
    {
        $this->queue[] = new ApiConnectionException('timeout');

        return $this;
    }

    public function request(string $method, string $url, array $headers, string|array|null $body, int $timeout): Response
    {
        if (str_ends_with(parse_url($url, PHP_URL_PATH), '/api/v1/authentication/login')) {
            $this->logins++;
            $this->requests[] = compact('method', 'url', 'headers', 'body') + ['login' => true];

            return new Response(201, [], (string) json_encode([
                'token'      => $this->token . ($this->logins > 1 ? '_' . $this->logins : ''),
                'expires_at' => gmdate('Y-m-d\TH:i:sO', strtotime($this->expiresAt)),
            ]));
        }

        $this->requests[] = compact('method', 'url', 'headers', 'body');

        $next = array_shift($this->queue) ?? new Response(200, [], '{}');
        if ($next instanceof ApiConnectionException) {
            throw $next;
        }

        return $next;
    }

    /**
     * Requests excluding logins.
     *
     * @return list<array{method: string, url: string, headers: array<string, string>, body: string|array|null}>
     */
    public function apiRequests(): array
    {
        return array_values(array_filter($this->requests, fn ($r) => empty($r['login'])));
    }

    public function last(): array
    {
        $api = $this->apiRequests();

        return $api[count($api) - 1];
    }

    public function lastBody(): array
    {
        return json_decode((string) $this->last()['body'], true);
    }
}
