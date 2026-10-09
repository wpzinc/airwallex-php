<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use WPZinc\Airwallex\AirwallexObject;
use WPZinc\Airwallex\Collection;

/**
 * Calls every generated method and checks it hits the documented endpoint.
 */
final class GeneratedServicesTest extends TestCase
{
    public static function endpoints(): iterable
    {
        $spec = json_decode((string) file_get_contents(dirname(__DIR__) . '/codegen/spec.json'), true);

        foreach ($spec['services'] as $service) {
            $prefix = $service['namespace'] === '' ? '' : lcfirst($service['namespace']) . '.';
            foreach ($service['methods'] as $method) {
                yield $prefix . $service['property'] . '.' . $method['name'] => [$service, $method];
            }
        }
    }

    #[DataProvider('endpoints')]
    public function testEndpoint(array $service, array $method): void
    {
        $factory = $this->client;
        if ($service['namespace'] !== '') {
            $factory = $factory->{lcfirst($service['namespace'])};
        }
        $svc = $factory->{$service['property']};

        preg_match_all('/\{([^}]+)\}/', $method['path'], $m);
        $ids      = array_map(fn ($i) => 'id' . $i, array_keys($m[1]));
        $expected = $method['path'];
        foreach ($ids as $id) {
            $expected = preg_replace('/\{[^}]+\}/', $id, $expected, 1);
        }

        $this->http->queue(200, $method['returns'] === 'collection' ? ['items' => [['id' => 'x']], 'has_more' => false] : ['id' => 'x']);

        $result = $svc->{$method['name']}(...[...$ids, ['foo' => 'bar']]);

        $req = $this->http->last();
        $this->assertSame($method['http'], $req['method']);
        $this->assertSame('https://api.sandbox.airwallex.com' . $expected . ($method['http'] === 'GET' ? '?foo=bar' : ''), $req['url']);

        if ($method['returns'] === 'collection') {
            $this->assertInstanceOf(Collection::class, $result);
            $this->assertSame('x', $result->first()->id);
        } else {
            $this->assertInstanceOf(AirwallexObject::class, $result);
            $this->assertSame('x', $result->id);
        }

        if ($method['http'] === 'POST') {
            $body = json_decode($req['body'], true);
            $this->assertSame('bar', $body['foo']);
            $this->assertSame(in_array($method['request_id'], ['required', 'optional'], true), isset($body['request_id']));
        }
    }

    public function testUnknownServiceThrows(): void
    {
        $this->expectException(\WPZinc\Airwallex\Exception\InvalidArgumentException::class);
        $this->client->nope;
    }
}
