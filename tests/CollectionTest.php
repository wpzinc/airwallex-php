<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests;

use WPZinc\Airwallex\Resource\Balance;
use WPZinc\Airwallex\Resource\Billing\Subscription;
use WPZinc\Airwallex\Resource\PaymentIntent;

final class CollectionTest extends TestCase
{
    public function testOffsetPagination(): void
    {
        $this->http
            ->queue(200, ['items' => [['id' => 'a'], ['id' => 'b']], 'has_more' => true])
            ->queue(200, ['items' => [['id' => 'c']], 'has_more' => false]);

        $page = $this->client->paymentIntents->all(['page_size' => 2]);

        $this->assertCount(2, $page);
        $this->assertInstanceOf(PaymentIntent::class, $page->first());
        $this->assertTrue($page->hasMore());
        $this->assertSame(['page_size' => 2, 'page_num' => 1], $page->nextPageParams());

        $ids = [];
        foreach ($page->autoPagingIterator() as $intent) {
            $ids[] = $intent->id;
        }

        $this->assertSame(['a', 'b', 'c'], $ids);
        $this->assertStringEndsWith('page_size=2&page_num=1', $this->http->last()['url']);
    }

    public function testCursorPagination(): void
    {
        $this->http
            ->queue(200, ['items' => [['id' => 'sub_1']], 'page_after' => 'cur_2'])
            ->queue(200, ['items' => [['id' => 'sub_2']], 'page_before' => 'cur_1']);

        $page = $this->client->billing->subscriptions->all(['status' => 'ACTIVE']);

        $this->assertInstanceOf(Subscription::class, $page->items()[0]);
        $next = $page->nextPage();
        $this->assertNotNull($next);
        $this->assertSame('sub_2', $next->first()->id);
        $this->assertStringEndsWith('status=ACTIVE&page=cur_2', $this->http->last()['url']);
        $this->assertFalse($next->hasMore());
        $this->assertSame(['status' => 'ACTIVE', 'page' => 'cur_1'], $next->previousPageParams());
    }

    public function testEmptyPageStopsPagination(): void
    {
        $this->http->queue(200, ['items' => [], 'page_after' => 'cur_x']);

        $page = $this->client->billing->subscriptions->all();

        $this->assertTrue($page->isEmpty());
        $this->assertFalse($page->hasMore());
        $this->assertSame([], iterator_to_array($page->autoPagingIterator()));
    }

    public function testTopLevelListResponse(): void
    {
        $this->http->queue(200, [['currency' => 'USD', 'available_amount' => 10], ['currency' => 'SGD', 'available_amount' => 5]]);

        $balances = $this->client->balances->current();

        $this->assertCount(2, $balances);
        $this->assertInstanceOf(Balance::class, $balances->items()[1]);
        $this->assertSame('SGD', $balances->items()[1]->currency);
        $this->assertFalse($balances->hasMore());
    }

    public function testToArrayIncludesItemsAndMeta(): void
    {
        $this->http->queue(200, ['items' => [['id' => 'a', 'meta' => ['k' => 'v']]], 'has_more' => false]);

        $array = $this->client->paymentIntents->all()->toArray();

        $this->assertSame(['has_more' => false, 'items' => [['id' => 'a', 'meta' => ['k' => 'v']]]], $array);
    }
}
