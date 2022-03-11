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
use Symfony\Component\Validator\Constraints\Compound\PrimitiveArray;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

class PrimitiveArrayTest extends TestCase
{
    /**
     * @dataProvider provideValidValue
     */
    public function testValidValue(array $value)
    {
        $this->assertCount(
            0,
            Validation::createValidator()->validate($value, new PrimitiveArray()),
        );
    }

    public function provideValidValue()
    {
        return [
            [[]],
            [['foo']],
            [['foo' => 'bar']],
        ];
    }

    public function testNull()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveArray()),
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
            $violations = Validation::createValidator()->validate($value, new PrimitiveArray()),
        );

        $this->assertSame('This value should be of type array.', $violations[0]->getMessage());
        $this->assertSame(Type::INVALID_TYPE_ERROR, $violations[0]->getCode());
    }

    public function provideInvalidType()
    {
        return [
            ['foo'],
            [true],
        ];
    }

    public function testCustomNotNullMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveArray([
                'notNullMessage' => 'foo bar ccc',
            ])),
        );

        $this->assertSame('foo bar ccc', $violations[0]->getMessage());
    }

    public function testCustomTypeMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(123, new PrimitiveArray([
                'typeMessage' => 'foo {{ type }} ccc',
            ])),
        );

        $this->assertSame('foo array ccc', $violations[0]->getMessage());
    }
}
