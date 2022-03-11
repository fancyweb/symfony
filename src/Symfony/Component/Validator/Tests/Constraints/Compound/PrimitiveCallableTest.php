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
use Symfony\Component\Validator\Constraints\Compound\PrimitiveCallable;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validation;

class PrimitiveCallableTest extends TestCase
{
    /**
     * @dataProvider provideValidValue
     */
    public function testValidValue(callable $value)
    {
        $this->assertCount(
            0,
            Validation::createValidator()->validate($value, new PrimitiveCallable()),
        );
    }

    public function provideValidValue()
    {
        return [
            ['strlen'],
            [Constraint::class.'::getErrorName'],
            [[$this, 'provideValidValue']],
            [static fn() => 'foo'],
            [new class() { public function __invoke(): string { return 'bar'; }}],
        ];
    }

    public function testNull()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveCallable()),
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
            $violations = Validation::createValidator()->validate($value, new PrimitiveCallable()),
        );

        $this->assertSame('This value should be of type callable.', $violations[0]->getMessage());
        $this->assertSame(Type::INVALID_TYPE_ERROR, $violations[0]->getCode());
    }

    public function provideInvalidType()
    {
        return [
            ['a_function_that_will_never_exist_b629341cb07b4fc7a327c95dc800b3a0'],
            [new \stdClass()],
            [true],
        ];
    }

    public function testCustomNotNullMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(null, new PrimitiveCallable([
                'notNullMessage' => 'foo bar ccc',
            ])),
        );

        $this->assertSame('foo bar ccc', $violations[0]->getMessage());
    }

    public function testCustomTypeMessage()
    {
        $this->assertCount(
            1,
            $violations = Validation::createValidator()->validate(123, new PrimitiveCallable([
                'typeMessage' => 'foo {{ type }} ccc',
            ])),
        );

        $this->assertSame('foo callable ccc', $violations[0]->getMessage());
    }
}
