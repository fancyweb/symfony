<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Constraints\Compound;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Compound\PrimitiveIterable;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

class PrimitiveIterableTest extends TestCase
{
    /**
     * @dataProvider provideValidValue
     */
    public function testValidValue(iterable $value)
    {
        $this->assertCount(
            0,
            Validation::createValidator()->validate($value, new PrimitiveIterable()),
        );
    }

    public function provideValidValue()
    {
        return [
            [[]],
            [new \ArrayIterator([])],
        ];
    }

    public function testNull()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveIterable()),
        );

        $this->assertSame('This value should not be null.', $violations[0]->getMessage());
        $this->assertSame(NotNull::IS_NULL_ERROR, $violations[0]->getCode());
    }

    /**
     * @dataProvider provideInvalidType
     */
    public function testInvalidType($value)
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate($value, new PrimitiveIterable()),
        );

        $this->assertSame('This value should be of type iterable.', $violations[0]->getMessage());
        $this->assertSame(Type::INVALID_TYPE_ERROR, $violations[0]->getCode());
    }

    public function provideInvalidType()
    {
        return [
            ['foo'],
            [new \stdClass()],
            [true],
        ];
    }

    public function testCustomNotNullMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveIterable([
                'notNullMessage' => 'foo bar ccc',
            ])),
        );

        $this->assertSame('foo bar ccc', $violations[0]->getMessage());
    }

    public function testCustomTypeMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(123, new PrimitiveIterable([
                'typeMessage' => 'foo {{ type }} ccc',
            ])),
        );

        $this->assertSame('foo iterable ccc', $violations[0]->getMessage());
    }
}
