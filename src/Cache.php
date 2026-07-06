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

use DateInterval;
use Phalcon\Bridge\Psr16\Exception\InvalidArgumentException;
use Phalcon\Cache\Adapter\AdapterInterface;
use Phalcon\Cache\Cache as PhalconCache;
use Phalcon\Cache\Exception\InvalidArgumentException as PhalconInvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

/**
 * Phalcon Bridge PSR-16 Cache.
 *
 * A PSR-16 cache backed by a Phalcon cache. It uses composition rather than
 * inheritance so the PSR-16 v3 signatures are declared here directly and stay
 * valid across both Phalcon runtimes - the phalcon/phalcon (v6) package and the
 * cphalcon (v5) extension, whose `Cache::get()` signature predates PSR-16 v3.
 * Illegal-key failures raised by the wrapped cache are re-thrown as the
 * PSR-typed InvalidArgumentException. Use it wherever a
 * Psr\SimpleCache\CacheInterface is expected.
 */
final class Cache implements CacheInterface
{
    private PhalconCache $cache;

    public function __construct(AdapterInterface $adapter)
    {
        $this->cache = new PhalconCache($adapter);
    }

    public function clear(): bool
    {
        return $this->cache->clear();
    }

    public function delete(string $key): bool
    {
        return $this->guard(fn (): bool => $this->cache->delete($key));
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return $this->guard(fn (): bool => $this->cache->deleteMultiple($keys));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->guard(fn (): mixed => $this->cache->get($key, $default));
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return $this->guard(fn (): iterable => $this->cache->getMultiple($keys, $default));
    }

    public function has(string $key): bool
    {
        return $this->guard(fn (): bool => $this->cache->has($key));
    }

    public function set(string $key, mixed $value, null | int | DateInterval $ttl = null): bool
    {
        return $this->guard(fn (): bool => $this->cache->set($key, $value, $ttl));
    }

    public function setMultiple(iterable $values, null | int | DateInterval $ttl = null): bool
    {
        return $this->guard(fn (): bool => $this->cache->setMultiple($values, $ttl));
    }

    /**
     * Runs the delegation and re-throws a Phalcon illegal-key exception as the
     * PSR-16 typed InvalidArgumentException.
     *
     * @template TReturn
     *
     * @param callable(): TReturn $callback
     *
     * @return TReturn
     */
    private function guard(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (PhalconInvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }
    }
}
