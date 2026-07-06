<?php

declare(strict_types=1);

namespace Phalcon\Bridge\Psr16\Tests\Support\Traits;

use Phalcon\Bridge\Psr16\Cache;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Storage\SerializerFactory;

trait CacheFactoryTrait
{
    private function makeCache(): Cache
    {
        $adapterFactory = new AdapterFactory(new SerializerFactory());

        return new Cache($adapterFactory->newInstance('memory'));
    }
}
