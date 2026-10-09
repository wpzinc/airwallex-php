<?php

declare(strict_types=1);

namespace WPZinc\Airwallex\Tests;

use PHPUnit\Framework\TestCase;
use WPZinc\Airwallex\AirwallexObject;

final class AirwallexObjectTest extends TestCase
{
    public function testAccessAndConversion(): void
    {
        $obj = AirwallexObject::constructFrom([
            'id'       => 'x',
            'customer' => ['email' => 'a@b.c', 'address' => ['country_code' => 'SG']],
            'items'    => [['id' => 'i1'], ['id' => 'i2']],
            'tags'     => ['a', 'b'],
            'empty'    => [],
        ]);

        $this->assertSame('x', $obj->id);
        $this->assertSame('SG', $obj->customer->address->country_code);
        $this->assertSame('i2', $obj->items[1]->id);
        $this->assertSame(['a', 'b'], $obj->tags);
        $this->assertNull($obj->missing);
        $this->assertTrue(isset($obj['customer']));
        $this->assertFalse(isset($obj->missing));
        $this->assertSame(['id', 'customer', 'items', 'tags', 'empty'], $obj->keys());
    }

    public function testRoundTrip(): void
    {
        $data = ['id' => 'x', 'nested' => ['a' => 1, 'list' => [['b' => 2]]], 'amount' => 10.0];
        $obj  = AirwallexObject::constructFrom($data);

        $this->assertSame($data, $obj->toArray());
        $this->assertSame('{"id":"x","nested":{"a":1,"list":[{"b":2}]},"amount":10.0}', json_encode($obj, JSON_PRESERVE_ZERO_FRACTION));
        $this->assertSame('{}', json_encode(new AirwallexObject()));
    }

    public function testMutation(): void
    {
        $obj           = new AirwallexObject();
        $obj->metadata = ['k' => 'v'];
        $obj['x']      = 1;
        unset($obj->x);

        $this->assertSame('v', $obj->metadata->k);
        $this->assertCount(1, $obj);
    }
}
