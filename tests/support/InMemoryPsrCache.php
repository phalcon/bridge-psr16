<?php

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16\Tests\Support;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

/**
 * A minimal in-memory PSR-16 cache used to exercise the import Adapter.
 */
final class InMemoryPsrCache implements CacheInterface
{
    /** @var array<string, mixed> */
    private array $data = [];

    public function clear(): bool
    {
        $this->data = [];

        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->data[$key]);

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function set(string $key, mixed $value, null | int | DateInterval $ttl = null): bool
    {
        $this->data[$key] = $value;

        return true;
    }

    public function setMultiple(iterable $values, null | int | DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }
}
