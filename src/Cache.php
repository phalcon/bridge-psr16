<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16;

use Phalcon\Bridge\Psr16\Exception\InvalidArgumentException;
use Phalcon\Cache\Cache as PhalconCache;
use Psr\SimpleCache\CacheInterface;

/**
 * Phalcon Bridge PSR-16 Cache.
 *
 * A PSR-16 cache backed by a Phalcon cache adapter. Reuses the entire
 * Phalcon\Cache\Cache implementation (all eight PSR-16-shaped methods plus
 * key validation and events); the only addition is routing illegal-key
 * failures through the PSR-typed exception. Use it wherever a
 * Psr\SimpleCache\CacheInterface is expected.
 */
class Cache extends PhalconCache implements CacheInterface
{
    /**
     * Route illegal-key / non-iterable failures through the PSR-16 typed
     * InvalidArgumentException instead of the Phalcon one.
     *
     * @return string
     */
    protected function getExceptionClass(): string
    {
        return InvalidArgumentException::class;
    }
}
