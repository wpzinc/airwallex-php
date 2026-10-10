<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests\Sandbox;

use WPZinc\Airwallex\Exception\IdempotencyException;
use WPZinc\Airwallex\Exception\InvalidRequestException;
use WPZinc\Airwallex\Exception\NotFoundException;
use WPZinc\Airwallex\Resource\Customer;
use WPZinc\Airwallex\Resource\PaymentIntent;

final class PaymentsTest extends SandboxTestCase
{
    public function testCustomerLifecycle(): void
    {
        $ref      = $this->uniqueId('cus');
        $customer = $this->airwallex->customers->create([
            'merchant_customer_id' => $ref,
            'email'                => $ref . '@example.com',
            'first_name'           => 'Sandbox',
            'last_name'            => 'Test',
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertNotEmpty($customer->id);
        $this->assertSame($ref, $customer->merchant_customer_id);

        $updated = $this->airwallex->customers->update($customer->id, ['first_name' => 'Updated']);
        $this->assertSame('Updated', $updated->first_name);

        $fetched = $this->airwallex->customers->retrieve($customer->id);
        $this->assertSame($customer->id, $fetched->id);

        $secret = $this->airwallex->customers->generateClientSecret($customer->id);
        $this->assertNotEmpty($secret->client_secret);
    }

    public function testPaymentIntentLifecycle(): void
    {
        $orderId = $this->uniqueId('order');
        $intent  = $this->airwallex->paymentIntents->create([
            'amount'            => 12.34,
            'currency'          => 'SGD',
            'merchant_order_id' => $orderId,
            'metadata'          => ['source' => 'airwallex-php sandbox test'],
        ]);

        $this->assertInstanceOf(PaymentIntent::class, $intent);
        $this->assertSame(12.34, (float) $intent->amount);
        $this->assertSame('REQUIRES_PAYMENT_METHOD', $intent->status);
        $this->assertNotEmpty($intent->client_secret);
        $this->assertSame('airwallex-php sandbox test', $intent->metadata->source);

        $updated = $this->airwallex->paymentIntents->update($intent->id, ['amount' => 20]);
        $this->assertSame(20.0, (float) $updated->amount);

        $cancelled = $this->airwallex->paymentIntents->cancel($intent->id, ['cancellation_reason' => 'Sandbox test']);
        $this->assertSame('CANCELLED', $cancelled->status);

        $page = $this->airwallex->paymentIntents->all(['merchant_order_id' => $orderId]);
        $this->assertSame($intent->id, $page->first()?->id);
    }

    public function testRequestIdIsIdempotent(): void
    {
        $requestId = $this->uniqueId('req');
        $params    = ['request_id' => $requestId, 'amount' => 5, 'currency' => 'SGD', 'merchant_order_id' => $this->uniqueId('order')];

        $this->airwallex->paymentIntents->create($params);

        // Airwallex rejects a reused request_id rather than replaying the response
        $this->expectException(IdempotencyException::class);
        $this->airwallex->paymentIntents->create($params);
    }

    public function testPaymentLink(): void
    {
        $link = $this->airwallex->paymentLinks->create([
            'title'    => 'SDK sandbox test',
            'amount'   => 9.99,
            'currency' => 'SGD',
            'reusable' => false,
        ]);

        $this->assertNotEmpty($link->id);
        $this->assertNotEmpty($link->url);

        $this->airwallex->paymentLinks->deactivate($link->id);
        $this->assertFalse((bool) $this->airwallex->paymentLinks->retrieve($link->id)->active);
    }

    public function testValidationErrorIsTyped(): void
    {
        try {
            $this->airwallex->paymentIntents->create(['currency' => 'SGD']);
            $this->fail('Expected InvalidRequestException');
        } catch (InvalidRequestException $e) {
            $this->assertSame(400, $e->getHttpStatus());
            $this->assertNotEmpty($e->getAirwallexCode());
            $this->assertNotEmpty($e->getMessage());
        }
    }

    public function testNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->airwallex->paymentIntents->retrieve('int_does_not_exist');
    }
}
