<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests\Sandbox;

use WPZinc\Airwallex\Resource\Billing\Checkout;
use WPZinc\Airwallex\Resource\Billing\Customer;
use WPZinc\Airwallex\Resource\Billing\Price;
use WPZinc\Airwallex\Resource\Billing\Product;

final class BillingTest extends SandboxTestCase
{
    public function testSubscriptionCheckoutFlow(): void
    {
        $billing = $this->airwallex->billing;
        $ref     = $this->uniqueId('bcus');

        $customer = $billing->customers->create([
            'name'  => 'Sandbox ' . $ref,
            'email' => $ref . '@example.com',
            'type'  => 'INDIVIDUAL',
        ]);
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertNotEmpty($customer->id);

        $product = $billing->products->create(['name' => 'SDK Pro ' . $ref]);
        $this->assertInstanceOf(Product::class, $product);

        $updated = $billing->products->update($product->id, ['description' => 'Updated by SDK']);
        $this->assertSame('Updated by SDK', $updated->description);

        $price = $billing->prices->create([
            'product_id'    => $product->id,
            'currency'      => 'USD',
            'pricing_model' => 'PER_UNIT',
            'unit_amount'   => 29,
            'recurring'     => ['period' => 1, 'period_unit' => 'MONTH'],
        ]);
        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame('MONTH', $price->recurring->period_unit);

        $checkout = $billing->checkouts->create([
            'mode'                => 'SUBSCRIPTION',
            'billing_customer_id' => $customer->id,
            'currency'            => 'USD',
            'line_items'          => [['price_id' => $price->id, 'quantity' => 1]],
            'subscription_data'   => ['metadata' => ['source' => 'airwallex-php']],
            'success_url'         => 'https://example.com/success',
            'back_url'            => 'https://example.com/back',
        ]);
        $this->assertInstanceOf(Checkout::class, $checkout);
        $this->assertNotEmpty($checkout->id);
        $this->assertNotEmpty($checkout->url);

        $this->assertSame($checkout->id, $billing->checkouts->retrieve($checkout->id)->id);

        $cancelled = $billing->checkouts->cancel($checkout->id);
        $this->assertNotEmpty($cancelled->status);

        // Deactivate so repeated runs don't clutter the catalogue
        $billing->prices->update($price->id, ['active' => false]);
        $billing->products->update($product->id, ['active' => false]);
    }

    public function testCursorAutoPagination(): void
    {
        $products = $this->airwallex->billing->products;
        $ref      = $this->uniqueId('page');
        $created  = [];
        for ($i = 0; $i < 3; $i++) {
            $created[] = $products->create(['name' => "$ref $i"])->id;
        }

        $page = $products->all(['page_size' => 1]);
        $this->assertCount(1, $page);
        $this->assertTrue($page->hasMore());

        $seen = [];
        foreach ($page->autoPagingIterator() as $product) {
            $seen[] = $product->id;
            if (count(array_intersect($created, $seen)) === 3 || count($seen) >= 50) {
                break;
            }
        }

        $this->assertSame([], array_diff($created, $seen));
        $this->assertSame(count($seen), count(array_unique($seen)));

        foreach ($created as $id) {
            $products->update($id, ['active' => false]);
        }
    }
}
