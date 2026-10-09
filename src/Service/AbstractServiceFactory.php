<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Service;

use WPZinc\Airwallex\ApiRequestor;
use WPZinc\Airwallex\Exception\InvalidArgumentException;

/**
 * Lazily creates services, accessed as properties (e.g. $client->billing->subscriptions).
 */
abstract class AbstractServiceFactory
{
    /** @var array<string, object> */
    private array $instances = [];

    public function __construct(protected readonly ApiRequestor $requestor)
    {
    }

    /**
     * Property name => service or factory class.
     *
     * @return array<string, class-string>
     */
    abstract protected function serviceClasses(): array;

    public function __get(string $name): object
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $class = $this->serviceClasses()[$name] ?? null;
        if ($class === null) {
            throw new InvalidArgumentException(sprintf('Unknown service "%s" on %s', $name, static::class));
        }

        return $this->instances[$name] = new $class($this->requestor);
    }

    public function __isset(string $name): bool
    {
        return isset($this->serviceClasses()[$name]);
    }

    /**
     * @return list<string>
     */
    public function services(): array
    {
        return array_keys($this->serviceClasses());
    }
}
