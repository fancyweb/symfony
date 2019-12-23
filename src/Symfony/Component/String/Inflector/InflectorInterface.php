<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String\Inflector;

use Symfony\Component\String\AbstractString;

/**
 * @experimental in 5.1
 */
interface InflectorInterface
{
    /**
     * Returns the singular form of a string.
     *
     * If the method can't determine the form with certainty, an array of the
     * possible singulars is returned.
     *
     * @return AbstractString|AbstractString[] The singular form or an array of possible singular forms
     */
    public function singularize(AbstractString $string);

    /**
     * Returns the plural form of a string.
     *
     * If the method can't determine the form with certainty, an array of the
     * possible plurals is returned.
     *
     * @return AbstractString|AbstractString[] The plural form or an array of possible plural forms
     */
    public function pluralize(AbstractString $string);
}
