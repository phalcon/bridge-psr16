<?php

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16\Tests\Unit\Cache;

use Phalcon\Bridge\Psr16\Tests\Support\Traits\CacheFactoryTrait;
use PHPUnit\Framework\TestCase;

use function is_array;
use function iterator_to_array;

final class RoundTripTest extends TestCase
{
    use CacheFactoryTrait;

    public function testClear(): void
    {
        $cache = $this->makeCache();
        $cache->set('x', 1);

        $this->assertTrue($cache->clear());
        $this->assertFalse($cache->has('x'));
    }

    public function testMultiple(): void
    {
        $cache = $this->makeCache();

        $this->assertTrue($cache->setMultiple(['a' => 1, 'b' => 2]));

        $values = $cache->getMultiple(['a', 'b']);
        $values = is_array($values) ? $values : iterator_to_array($values);
        $this->assertSame(['a' => 1, 'b' => 2], $values);

        $this->assertTrue($cache->deleteMultiple(['a', 'b']));
        $this->assertFalse($cache->has('a'));
        $this->assertFalse($cache->has('b'));
    }

    public function testSetGetHasDelete(): void
    {
        $cache = $this->makeCache();

        $this->assertFalse($cache->has('name'));
        $this->assertNull($cache->get('name'));
        $this->assertSame('default', $cache->get('name', 'default'));

        $this->assertTrue($cache->set('name', 'Phalcon'));
        $this->assertTrue($cache->has('name'));
        $this->assertSame('Phalcon', $cache->get('name'));

        $this->assertTrue($cache->delete('name'));
        $this->assertFalse($cache->has('name'));
    }
}
