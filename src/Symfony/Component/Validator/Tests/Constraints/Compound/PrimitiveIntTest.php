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
use Symfony\Component\Validator\Constraints\Compound\PrimitiveInt;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

class PrimitiveIntTest extends TestCase
{
    /**
     * @dataProvider provideValidValue
     */
    public function testValidValue(int $value)
    {
        $this->assertCount(
            0,
            Validation::createValidator()->validate($value, new PrimitiveInt()),
        );
    }

    public function provideValidValue()
    {
        return [
            [0],
            [-123],
            [123456789],
        ];
    }

    public function testNull()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveInt()),
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
            $violations = Validation::createValidator()->validate($value, new PrimitiveInt()),
        );

        $this->assertSame('This value should be of type int.', $violations[0]->getMessage());
        $this->assertSame(Type::INVALID_TYPE_ERROR, $violations[0]->getCode());
    }

    public function provideInvalidType()
    {
        return [
            ['foo'],
            ['-123'],
            ['123'],
            ['123.123'],
            ['-123.123'],
            [123.0],
            [123.123],
            [-123.123],
            [true],
        ];
    }

    public function testCustomNotNullMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveInt([
                'notNullMessage' => 'foo bar ccc',
            ])),
        );

        $this->assertSame('foo bar ccc', $violations[0]->getMessage());
    }

    public function testCustomTypeMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate('foo', new PrimitiveInt([
                'typeMessage' => 'foo {{ type }} ccc',
            ])),
        );

        $this->assertSame('foo int ccc', $violations[0]->getMessage());
    }
}
