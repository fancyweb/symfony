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
use Symfony\Component\Validator\Constraints\Compound\PrimitiveBool;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

class PrimitiveBoolTest extends TestCase
{
    /**
     * @dataProvider provideValidValue
     */
    public function testValidValue(bool $value)
    {
        $this->assertCount(
            0,
            Validation::createValidator()->validate($value, new PrimitiveBool()),
        );
    }

    public function provideValidValue()
    {
        return [
            [true],
            [false],
        ];
    }

    public function testNull()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveBool()),
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
            $violations = Validation::createValidator()->validate($value, new PrimitiveBool()),
        );

        $this->assertSame('This value should be of type bool.', $violations[0]->getMessage());
        $this->assertSame(Type::INVALID_TYPE_ERROR, $violations[0]->getCode());
    }

    public function provideInvalidType()
    {
        return [
            ['foo'],
            [0],
            [1],
            ['0'],
            ['1'],
            [[]],
        ];
    }

    public function testCustomNotNullMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveBool([
                'notNullMessage' => 'foo bar ccc',
            ])),
        );

        $this->assertSame('foo bar ccc', $violations[0]->getMessage());
    }

    public function testCustomTypeMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(123, new PrimitiveBool([
                'typeMessage' => 'foo {{ type }} ccc',
            ])),
        );

        $this->assertSame('foo bool ccc', $violations[0]->getMessage());
    }
}
