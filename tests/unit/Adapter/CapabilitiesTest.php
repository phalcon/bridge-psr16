<?php

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16\Tests\Unit\Adapter;

use Phalcon\Bridge\Psr16\Adapter;
use Phalcon\Bridge\Psr16\Tests\Support\InMemoryPsrCache;
use Phalcon\Storage\SerializerFactory;
use PHPUnit\Framework\TestCase;

final class CapabilitiesTest extends TestCase
{
    public function testDecrementMissingKeyReturnsFalse(): void
    {
        $adapter = new Adapter(new SerializerFactory(), new InMemoryPsrCache());

        $this->assertFalse($adapter->decrement('missing'));
    }

    public function testGetAdapterReturnsWrappedCache(): void
    {
        $psr     = new InMemoryPsrCache();
        $adapter = new Adapter(new SerializerFactory(), $psr);

        $this->assertSame($psr, $adapter->getAdapter());
    }

    public function testGetKeysIsEmpty(): void
    {
        $adapter = new Adapter(new SerializerFactory(), new InMemoryPsrCache());

        $this->assertSame([], $adapter->getKeys());
        $this->assertSame([], $adapter->getKeys('prefix-'));
    }

    public function testSetForeverStoresInPsrCache(): void
    {
        $psr     = new InMemoryPsrCache();
        $adapter = new Adapter(new SerializerFactory(), $psr);

        $this->assertTrue($adapter->setForever('perma', 'value'));
        $this->assertTrue($psr->has('perma'));
        $this->assertSame('value', $psr->get('perma'));
    }

    public function testSetWithNonPositiveTtlDeletesKey(): void
    {
        $psr     = new InMemoryPsrCache();
        $adapter = new Adapter(new SerializerFactory(), $psr);

        $adapter->set('temp', 'value');
        $this->assertTrue($psr->has('temp'));

        // A TTL of <= 0 means the item is already expired, so it is deleted.
        $this->assertTrue($adapter->set('temp', 'value', 0));
        $this->assertFalse($psr->has('temp'));
    }
}
