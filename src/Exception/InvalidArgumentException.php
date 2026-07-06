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

namespace Phalcon\Bridge\Psr16\Exception;

use Exception;
use Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentException;

/**
 * PSR-16 typed invalid-argument exception. Relocated out of Phalcon core so
 * the core stays free of any `psr/*` dependency.
 */
class InvalidArgumentException extends Exception implements SimpleCacheInvalidArgumentException
{
}
