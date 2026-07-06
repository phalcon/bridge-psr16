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

use Phalcon\Cache\Adapter\AdapterInterface;
use Phalcon\Storage\Adapter\AbstractAdapter;
use Phalcon\Storage\SerializerFactory;
use Psr\SimpleCache\CacheInterface;

use function is_int;

/**
 * Phalcon Bridge PSR-16 Adapter.
 *
 * A Phalcon cache adapter that forwards to a wrapped PSR-16 cache, so any
 * PSR-16 implementation can back a Phalcon\Cache\Cache. Serialization is
 * delegated to the PSR-16 cache (`defaultSerializer = 'none'`) to avoid
 * double-encoding.
 */
class Adapter extends AbstractAdapter implements AdapterInterface
{
    public function __construct(
        SerializerFactory $factory,
        protected CacheInterface $psrCache,
        array $options = []
    ) {
        // The PSR-16 cache owns serialization; keys pass through untouched
        // (no Phalcon prefix) so they match what the wrapped cache sees.
        $options['defaultSerializer'] = 'none';
        $options['prefix']            = $options['prefix'] ?? '';

        parent::__construct($factory, $options);

        $this->initSerializer();
    }

    public function clear(): bool
    {
        return $this->psrCache->clear();
    }

    public function getAdapter(): mixed
    {
        return $this->psrCache;
    }

    /**
     * PSR-16 cannot enumerate keys, so an empty list is returned.
     *
     * @param string $prefix
     *
     * @return array
     */
    public function getKeys(string $prefix = ''): array
    {
        return [];
    }

    public function setForever(string $key, mixed $data): bool
    {
        return $this->psrCache->set($this->getPrefixedKey($key), $data);
    }

    /**
     * PSR-16 has no atomic counters; emulated non-atomically.
     *
     * @param string $key
     * @param int    $value
     *
     * @return false|int
     */
    protected function doDecrement(string $key, int $value = 1): false | int
    {
        return $this->delta($key, -$value);
    }

    protected function doDelete(string $key): bool
    {
        return $this->psrCache->delete($this->getPrefixedKey($key));
    }

    protected function doGetData(string $key): mixed
    {
        return $this->psrCache->get($this->getPrefixedKey($key));
    }

    protected function doHas(string $key): bool
    {
        return $this->psrCache->has($this->getPrefixedKey($key));
    }

    protected function doIncrement(string $key, int $value = 1): false | int
    {
        return $this->delta($key, $value);
    }

    protected function doSet(string $key, mixed $value, mixed $ttl = null): bool
    {
        if (is_int($ttl) && $ttl < 1) {
            return $this->delete($key);
        }

        return $this->psrCache->set($this->getPrefixedKey($key), $value, $ttl);
    }

    private function delta(string $key, int $value): false | int
    {
        $prefixedKey = $this->getPrefixedKey($key);

        if (false === $this->psrCache->has($prefixedKey)) {
            return false;
        }

        $newValue = (int) $this->psrCache->get($prefixedKey) + $value;
        $this->psrCache->set($prefixedKey, $newValue);

        return $newValue;
    }
}
