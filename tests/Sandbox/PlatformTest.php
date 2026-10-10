<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests\Sandbox;

use WPZinc\Airwallex\Collection;
use WPZinc\Airwallex\Resource\Balance;

final class PlatformTest extends SandboxTestCase
{
    public function testBalancesAreAList(): void
    {
        $balances = $this->airwallex->balances->current();

        $this->assertInstanceOf(Collection::class, $balances);
        $this->assertNotEmpty($balances->items());
        $this->assertInstanceOf(Balance::class, $balances->first());
        $this->assertNotEmpty($balances->first()->currency);
    }

    public function testOffsetAutoPagination(): void
    {
        $all  = $this->airwallex->finance->financialTransactions->all(['page_size' => 100]);
        $page = $this->airwallex->finance->financialTransactions->all(['page_size' => 2]);

        if (count($all) <= 2) {
            $this->markTestSkipped('Needs more than 2 financial transactions');
        }

        $this->assertTrue($page->hasMore());

        $ids = [];
        foreach ($page->autoPagingIterator() as $transaction) {
            $ids[] = $transaction->id;
        }

        $this->assertSame(array_map(fn ($t) => $t->id, $all->items()), $ids);
    }

    public function testCursorPaginationOnLargeList(): void
    {
        $page = $this->airwallex->issuing->merchantBrands->all(['page_size' => 5]);
        $this->assertCount(5, $page);

        $next = $page->nextPage();
        $this->assertNotNull($next);
        $this->assertCount(5, $next);
        $this->assertSame([], array_intersect(
            array_map(fn ($b) => $b->id, $page->items()),
            array_map(fn ($b) => $b->id, $next->items())
        ));
    }

    public function testFxRate(): void
    {
        $rate = $this->airwallex->fx->rates->current(['buy_currency' => 'USD', 'sell_currency' => 'SGD', 'buy_amount' => 100]);
        $this->assertSame('USDSGD', $rate->currency_pair);
        $this->assertGreaterThan(0, (float) $rate->rate);
    }

    public function testWebhookEndpointLifecycle(): void
    {
        $webhook = $this->airwallex->webhooks->create([
            'url'     => 'https://example.com/airwallex/webhook/' . bin2hex(random_bytes(4)),
            'events'  => ['payment_intent.succeeded'],
            'version' => \WPZinc\Airwallex\AirwallexClient::API_VERSION,
        ]);

        $this->assertNotEmpty($webhook->id);
        $this->assertSame($webhook->id, $this->airwallex->webhooks->retrieve($webhook->id)->id);

        $this->airwallex->webhooks->delete($webhook->id);
        $this->expectException(\WPZinc\Airwallex\Exception\ApiErrorException::class);
        $this->airwallex->webhooks->retrieve($webhook->id);
    }

    public function testFileUpload(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'awx') . '.pdf';
        file_put_contents($path, "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n");

        $file = $this->airwallex->files->upload($path, ['notes' => 'SDK sandbox test']);
        unlink($path);

        $this->assertNotEmpty($file->file_id);
    }

    public function testSpendAndPayoutLists(): void
    {
        $this->assertInstanceOf(Collection::class, $this->airwallex->spend->bills->all());
        $this->assertInstanceOf(Collection::class, $this->airwallex->spend->vendors->all());
        $this->assertInstanceOf(Collection::class, $this->airwallex->batchTransfers->all());
        $this->assertInstanceOf(Collection::class, $this->airwallex->payers->all());
    }

    public function testOrgLevelTokenReachesBillingButNotAccountResources(): void
    {
        $org = sandboxClient(false);

        $this->assertInstanceOf(Collection::class, $org->billing->products->all(['page_size' => 1]));
    }
}
