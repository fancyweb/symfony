<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Constraints\Compound;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @internal
 */
abstract class PrimitiveType extends Compound
{
    public ?string $notNullMessage = null;
    public ?string $typeMessage = null;

    /**
     * {@inheritdoc}
     */
    final protected function getConstraints(array $options): array
    {
        return [
            // Both constraints are necessarily mutually exclusive, so we can use Sequentially to avoid validating the type if the value is null
            new Sequentially([
                new NotNull(null, $options['notNullMessage'] ?? null),
                new Type($this->getPrimitiveType(), $options['typeMessage'] ?? null),
            ]),
        ];
    }

    abstract protected function getPrimitiveType(): string;
}
