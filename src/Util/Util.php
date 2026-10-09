<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Util;

use WPZinc\Airwallex\Exception\InvalidArgumentException;

/**
 * Internal helpers.
 */
final class Util
{
    /**
     * Random RFC 4122 v4 UUID, used for request_id.
     */
    public static function uuid(): string
    {
        $bytes    = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    /**
     * Replace {placeholders} in a path template with URL-encoded values, in order.
     */
    public static function buildPath(string $template, string ...$ids): string
    {
        $i    = 0;
        $path = preg_replace_callback(
            '/\{[^}]+\}/',
            static function (array $m) use (&$i, $ids, $template): string {
                if (! isset($ids[$i]) || trim($ids[$i]) === '') {
                    throw new InvalidArgumentException(sprintf('Missing or empty %s for %s', $m[0], $template));
                }
                return rawurlencode($ids[$i++]);
            },
            $template
        );

        return (string) $path;
    }

    /**
     * Encode query params, with booleans as true/false and lists as repeated keys.
     *
     * @param array<string, mixed> $params
     */
    public static function encodeQuery(array $params): string
    {
        $pairs = [];
        foreach ($params as $key => $value) {
            if ($value === null) {
                continue;
            }
            foreach (is_array($value) ? $value : [$value] as $item) {
                if (is_bool($item)) {
                    $item = $item ? 'true' : 'false';
                } elseif ($item instanceof \DateTimeInterface) {
                    $item = $item->format(\DateTimeInterface::ATOM);
                }
                $pairs[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $item);
            }
        }

        return implode('&', $pairs);
    }

    /**
     * Whether an array is a list (sequential integer keys from 0).
     */
    public static function isList(mixed $value): bool
    {
        return is_array($value) && array_is_list($value);
    }
}
