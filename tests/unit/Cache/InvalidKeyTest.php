<?php

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16\Tests\Unit\Cache;

use Phalcon\Bridge\Psr16\Exception\InvalidArgumentException;
use Phalcon\Bridge\Psr16\Tests\Support\Traits\CacheFactoryTrait;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentException;

final class InvalidKeyTest extends TestCase
{
    use CacheFactoryTrait;

    public function testExceptionIsPsrTyped(): void
    {
        try {
            $this->makeCache()->get('bad{key}');
            $this->fail('Expected an InvalidArgumentException');
        } catch (SimpleCacheInvalidArgumentException $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        }
    }

    public function testIllegalKeyThrowsBridgeException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeCache()->get('bad{key}');
    }
}
