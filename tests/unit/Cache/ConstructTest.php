<?php

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16\Tests\Unit\Cache;

use Phalcon\Bridge\Psr16\Tests\Support\Traits\CacheFactoryTrait;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

final class ConstructTest extends TestCase
{
    use CacheFactoryTrait;

    public function testIsPsrCache(): void
    {
        $this->assertInstanceOf(CacheInterface::class, $this->makeCache());
    }
}
