<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Filesystem\Retry;

interface RetryStrategyInterface
{
    /**
     * Returns the time to wait in milliseconds.
     */
    public function getDelay(int $retryCount): int;
}
