<?php declare(strict_types=1);

namespace Shuwei\Tests\Unit\Core\Framework\Rule\Container;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Rule\Container\AndRule;
use Shuwei\Core\Framework\Rule\Rule;
use Shuwei\Core\Framework\Rule\RuleScope;
use Shuwei\Core\Framework\Validation\Constraint\ArrayOfType;

/**
 * @package business-ops
 *
 * @covers \Shuwei\Core\Framework\Rule\Container\AndRule
 * @covers \Shuwei\Core\Framework\Rule\Container\Container
 *
 * @internal
 */
class AndRuleTest extends TestCase
{
    public function testAndRuleNameIsStillTheSame(): void
    {
        static::assertSame('andContainer', (new AndRule())->getName());
    }


    public function testConstraintsAreStillTheSame(): void
    {
        static::assertEquals(
            ['rules' => [new ArrayOfType(Rule::class)]],
            (new AndRule())->getConstraints()
        );
    }
}
