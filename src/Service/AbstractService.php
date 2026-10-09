<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Service;

use WPZinc\Airwallex\AirwallexObject;
use WPZinc\Airwallex\ApiRequestor;
use WPZinc\Airwallex\Collection;
use WPZinc\Airwallex\Util\Util;

/**
 * Base class for API services.
 */
abstract class AbstractService
{
    public function __construct(protected readonly ApiRequestor $requestor)
    {
    }

    /**
     * Request an endpoint returning a single object.
     *
     * @template T of AirwallexObject
     *
     * @param class-string<T>      $class
     * @param array<string, mixed> $params
     * @param array<string, mixed> $opts
     *
     * @return T
     */
    protected function request(string $method, string $path, array $params, array $opts, string $class = AirwallexObject::class, bool $requestId = false): AirwallexObject
    {
        [$json, $response] = $this->requestor->request($method, $path, $params, $opts, $requestId);

        return $class::constructFrom(is_array($json) ? $json : [], $response);
    }

    /**
     * Request a list endpoint, returning a page that can auto-paginate.
     *
     * @template T of AirwallexObject
     *
     * @param class-string<T>      $class
     * @param array<string, mixed> $params
     * @param array<string, mixed> $opts
     *
     * @return Collection<T>
     */
    protected function requestCollection(string $method, string $path, array $params, array $opts, string $class = AirwallexObject::class, bool $requestId = false): Collection
    {
        $fetcher = function (array $nextParams) use ($method, $path, $opts, $class, $requestId): Collection {
            return $this->requestCollection($method, $path, $nextParams, $opts, $class, $requestId);
        };

        [$json, $response] = $this->requestor->request($method, $path, $params, $opts, $requestId);

        return Collection::fromResponse(is_array($json) ? $json : [], $class, $params, $fetcher, $response);
    }

    /**
     * Interpolate path parameters.
     */
    protected function buildPath(string $template, string ...$ids): string
    {
        return Util::buildPath($template, ...$ids);
    }
}
