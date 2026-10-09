<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests;

use PHPUnit\Framework\TestCase;
use WPZinc\Airwallex\Event;
use WPZinc\Airwallex\Exception\SignatureVerificationException;
use WPZinc\Airwallex\Webhook;

final class WebhookTest extends TestCase
{
    private const SECRET = 'whsec_test';

    private string $payload = '{"id":"evt_1","name":"payment_intent.succeeded","account_id":"acct_1","data":{"object":{"id":"int_1","status":"SUCCEEDED"}},"created_at":"2026-10-08T10:00:00+0000","version":"2026-08-21"}';

    public function testConstructsVerifiedEvent(): void
    {
        $ts  = (string) (time() * 1000);
        $sig = hash_hmac('sha256', $ts . $this->payload, self::SECRET);

        $event = Webhook::constructEvent($this->payload, $sig, $ts, self::SECRET);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertSame('payment_intent.succeeded', $event->type());
        $this->assertSame('int_1', $event->object()->id);
        $this->assertSame('SUCCEEDED', $event->data->object->status);
    }

    public function testFromHeadersIsCaseInsensitive(): void
    {
        $ts  = (string) (time() * 1000);
        $sig = Webhook::computeSignature($this->payload, $ts, self::SECRET);

        $event = Webhook::constructEventFromHeaders($this->payload, ['X-Signature' => [$sig], 'X-Timestamp' => $ts], self::SECRET);

        $this->assertSame('evt_1', $event->id);
    }

    public function testRejectsBadSignature(): void
    {
        $this->expectException(SignatureVerificationException::class);
        Webhook::constructEvent($this->payload, str_repeat('0', 64), (string) (time() * 1000), self::SECRET);
    }

    public function testRejectsTamperedPayload(): void
    {
        $ts  = (string) (time() * 1000);
        $sig = Webhook::computeSignature($this->payload, $ts, self::SECRET);

        $this->expectException(SignatureVerificationException::class);
        Webhook::constructEvent(str_replace('SUCCEEDED', 'FAILED', $this->payload), $sig, $ts, self::SECRET);
    }

    public function testRejectsOldTimestamp(): void
    {
        $ts  = (string) ((time() - 600) * 1000);
        $sig = Webhook::computeSignature($this->payload, $ts, self::SECRET);

        $this->expectException(SignatureVerificationException::class);
        $this->expectExceptionMessage('tolerance');
        Webhook::constructEvent($this->payload, $sig, $ts, self::SECRET);
    }

    public function testToleranceCanBeDisabled(): void
    {
        $ts  = '1000';
        $sig = Webhook::computeSignature($this->payload, $ts, self::SECRET);

        $this->assertSame('evt_1', Webhook::constructEvent($this->payload, $sig, $ts, self::SECRET, null)->id);
    }

    public function testRejectsMissingHeaders(): void
    {
        $this->expectException(SignatureVerificationException::class);
        Webhook::constructEventFromHeaders($this->payload, [], self::SECRET);
    }

    public function testRejectsNonJsonPayload(): void
    {
        $ts  = (string) (time() * 1000);
        $sig = Webhook::computeSignature('nope', $ts, self::SECRET);

        $this->expectException(SignatureVerificationException::class);
        Webhook::constructEvent('nope', $sig, $ts, self::SECRET);
    }
}
