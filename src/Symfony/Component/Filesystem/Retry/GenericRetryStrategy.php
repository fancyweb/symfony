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

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;

class GenericRetryStrategy implements RetryStrategyInterface
{
    private $delayMs;
    private $multiplier;
    private $maxDelayMs;
    private $jitter;

    /**
     * @param int   $delayMs     Amount of time to delay (or the initial value when multiplier is used)
     * @param float $multiplier  Multiplier to apply to the delay each time a retry occurs
     * @param int   $maxDelayMs  Maximum delay to allow (0 means no maximum)
     * @param float $jitter      Probability of randomness int delay (0 = none, 1 = 100% random)
     */
    public function __construct(int $delayMs = 1000, float $multiplier = 2.0, int $maxDelayMs = 0, float $jitter = 0.1)
    {
        if ($delayMs < 0) {
            throw new InvalidArgumentException(sprintf('Delay must be greater than or equal to zero: "%s" given.', $delayMs));
        }
        $this->delayMs = $delayMs;

        if ($multiplier < 1) {
            throw new InvalidArgumentException(sprintf('Multiplier must be greater than or equal to one: "%s" given.', $multiplier));
        }
        $this->multiplier = $multiplier;

        if ($maxDelayMs < 0) {
            throw new InvalidArgumentException(sprintf('Max delay must be greater than or equal to zero: "%s" given.', $maxDelayMs));
        }
        $this->maxDelayMs = $maxDelayMs;

        if ($jitter < 0 || $jitter > 1) {
            throw new InvalidArgumentException(sprintf('Jitter must be between 0 and 1: "%s" given.', $jitter));
        }
        $this->jitter = $jitter;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelay(int $retryCount): int
    {
        $delay = $this->delayMs * $this->multiplier ** $retryCount;
        dump($delay);

        if ($this->jitter > 0) {
            $randomness = $delay * $this->jitter;
            $delay = $delay + random_int(-$randomness, +$randomness);
        }

        if ($delay > $this->maxDelayMs && 0 !== $this->maxDelayMs) {
            return $this->maxDelayMs;
        }

        return (int) $delay;
    }
}
