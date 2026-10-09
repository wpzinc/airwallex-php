<?php

declare(strict_types=1);

namespace WPZinc\Airwallex;

use WPZinc\Airwallex\Exception\SignatureVerificationException;

/**
 * Verify and parse incoming webhook events.
 *
 * Airwallex signs each delivery with HMAC-SHA256 over x-timestamp . raw body,
 * keyed with the webhook's secret, sent as hex in x-signature.
 */
final class Webhook
{
    /** Default allowed clock skew in seconds. */
    public const DEFAULT_TOLERANCE = 300;

    /**
     * Verify the signature and return the event.
     *
     * @param string   $payload   Raw request body, exactly as received.
     * @param string   $signature Value of the x-signature header.
     * @param string   $timestamp Value of the x-timestamp header (Unix milliseconds).
     * @param string   $secret    The webhook's secret.
     * @param int|null $tolerance Max age in seconds, or null to skip the timestamp check.
     *
     * @throws SignatureVerificationException
     */
    public static function constructEvent(
        string $payload,
        string $signature,
        string $timestamp,
        string $secret,
        ?int $tolerance = self::DEFAULT_TOLERANCE,
    ): Event {
        self::verifySignature($payload, $signature, $timestamp, $secret, $tolerance);

        try {
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (\JsonException $e) {
            throw new SignatureVerificationException('Invalid webhook payload: ' . $e->getMessage(), 0, $e);
        }

        if (! is_array($data) || array_is_list($data)) {
            throw new SignatureVerificationException('Invalid webhook payload: expected a JSON object');
        }

        return Event::constructFrom($data);
    }

    /**
     * Same as constructEvent(), reading x-signature and x-timestamp from a headers array.
     *
     * @param array<string, string|list<string>> $headers Request headers (any case).
     *
     * @throws SignatureVerificationException
     */
    public static function constructEventFromHeaders(
        string $payload,
        array $headers,
        string $secret,
        ?int $tolerance = self::DEFAULT_TOLERANCE,
    ): Event {
        $headers = array_change_key_case($headers, CASE_LOWER);
        $read    = static fn (string $name): string => (string) (is_array($headers[$name] ?? null) ? ($headers[$name][0] ?? '') : ($headers[$name] ?? ''));

        return self::constructEvent($payload, $read('x-signature'), $read('x-timestamp'), $secret, $tolerance);
    }

    /**
     * @throws SignatureVerificationException
     */
    public static function verifySignature(
        string $payload,
        string $signature,
        string $timestamp,
        string $secret,
        ?int $tolerance = self::DEFAULT_TOLERANCE,
        ?int $now = null,
    ): true {
        if ($signature === '' || $timestamp === '') {
            throw new SignatureVerificationException('Missing x-signature or x-timestamp header');
        }

        if (! ctype_digit($timestamp)) {
            throw new SignatureVerificationException('Invalid x-timestamp header');
        }

        $expected = hash_hmac('sha256', $timestamp . $payload, $secret);
        if (! hash_equals($expected, strtolower(trim($signature)))) {
            throw new SignatureVerificationException('Webhook signature does not match');
        }

        if ($tolerance !== null) {
            $ageSeconds = abs(($now ?? time()) - intdiv((int) $timestamp, 1000));
            if ($ageSeconds > $tolerance) {
                throw new SignatureVerificationException('Webhook timestamp is outside the tolerance window');
            }
        }

        return true;
    }

    /**
     * Build a signature, e.g. for testing your webhook handler.
     */
    public static function computeSignature(string $payload, string $timestamp, string $secret): string
    {
        return hash_hmac('sha256', $timestamp . $payload, $secret);
    }
}
