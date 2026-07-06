<?php

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16\Tests\Unit\Adapter;

use Phalcon\Bridge\Psr16\Adapter;
use Phalcon\Bridge\Psr16\Tests\Support\InMemoryPsrCache;
use Phalcon\Cache\Adapter\AdapterInterface;
use Phalcon\Cache\Cache;
use Phalcon\Storage\SerializerFactory;
use PHPUnit\Framework\TestCase;

final class RoundTripTest extends TestCase
{
    public function testClearThroughFacade(): void
    {
        $psr   = new InMemoryPsrCache();
        $cache = new Cache(new Adapter(new SerializerFactory(), $psr));

        $cache->set('a', 1);
        $this->assertTrue($cache->clear());
        $this->assertFalse($cache->has('a'));
    }

    public function testCounters(): void
    {
        $adapter = new Adapter(new SerializerFactory(), new InMemoryPsrCache());

        $adapter->set('hits', 10);
        $this->assertSame(11, $adapter->increment('hits'));
        $this->assertSame(9, $adapter->decrement('hits', 2));
        $this->assertFalse($adapter->increment('missing'));
    }

    public function testIsCacheAdapter(): void
    {
        $adapter = new Adapter(new SerializerFactory(), new InMemoryPsrCache());

        $this->assertInstanceOf(AdapterInterface::class, $adapter);
    }

    public function testPhalconCacheBackedByPsr(): void
    {
        $psr   = new InMemoryPsrCache();
        $cache = new Cache(new Adapter(new SerializerFactory(), $psr));

        $this->assertTrue($cache->set('name', 'Phalcon'));
        $this->assertTrue($cache->has('name'));
        $this->assertSame('Phalcon', $cache->get('name'));

        $this->assertTrue($psr->has('name'));

        $this->assertTrue($cache->delete('name'));
        $this->assertFalse($cache->has('name'));
    }
}
