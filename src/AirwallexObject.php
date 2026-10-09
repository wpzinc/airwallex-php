<?php

declare(strict_types=1);

namespace WPZinc\Airwallex;

use WPZinc\Airwallex\HttpClient\Response;

/**
 * An API resource. Fields are readable as properties ($obj->id) or array keys ($obj['id']).
 *
 * Nested objects are AirwallexObject instances too; lists stay as PHP arrays.
 *
 * @implements \ArrayAccess<string, mixed>
 * @implements \IteratorAggregate<string, mixed>
 */
class AirwallexObject implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable
{
    /** @var array<string, mixed> */
    protected array $values = [];

    protected ?Response $lastResponse = null;

    /**
     * @param array<string, mixed> $values
     */
    final public function __construct(array $values = [])
    {
        $this->refreshFrom($values);
    }

    /**
     * @param array<string, mixed> $values
     */
    public static function constructFrom(array $values, ?Response $response = null): static
    {
        $obj               = new static($values);
        $obj->lastResponse = $response;

        return $obj;
    }

    /**
     * @param array<string, mixed> $values
     */
    protected function refreshFrom(array $values): void
    {
        $this->values = [];
        foreach ($values as $key => $value) {
            $this->values[(string) $key] = self::convert($value);
        }
    }

    /**
     * Convert nested associative arrays to objects, recursively.
     */
    protected static function convert(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map([self::class, 'convert'], $value);
        }

        return new AirwallexObject($value);
    }

    public function __get(string $name): mixed
    {
        return $this->values[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->values[$name] = self::convert($value);
    }

    public function __isset(string $name): bool
    {
        return isset($this->values[$name]);
    }

    public function __unset(string $name): void
    {
        unset($this->values[$name]);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists((string) $offset, $this->values);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[(string) $offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[(string) $offset] = self::convert($value);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[(string) $offset]);
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->values);
    }

    /**
     * Plain array of all values, recursively.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return self::unconvert($this->values);
    }

    protected static function unconvert(mixed $value): mixed
    {
        if ($value instanceof self) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map([self::class, 'unconvert'], $value);
        }

        return $value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->values === [] ? new \stdClass() : $this->toArray();
    }

    public function toJson(int $flags = JSON_PRETTY_PRINT): string
    {
        return (string) json_encode($this->jsonSerialize(), $flags | JSON_UNESCAPED_SLASHES);
    }

    public function __toString(): string
    {
        return static::class . ' JSON: ' . $this->toJson();
    }

    /**
     * The HTTP response this object was built from, if any.
     */
    public function getLastResponse(): ?Response
    {
        return $this->lastResponse;
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return $this->values;
    }
}
