<?php declare(strict_types=1);

namespace Shuwei\Tests\Unit\Core\Framework\Rule\Container;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Checkout\Test\Cart\Common\FalseRule;
use Shuwei\Core\Checkout\Test\Cart\Common\TrueRule;
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
    /**
     * @dataProvider cases
     */
    public function testRuleLogic(AndRule $rule, bool $matching): void
    {
        $scope = $this->createMock(RuleScope::class);
        static::assertSame($matching, $rule->match($scope));
    }

    public function testAndRuleNameIsStillTheSame(): void
    {
        static::assertSame('andContainer', (new AndRule())->getName());
    }

    public function testICanAddRulesAfterwards(): void
    {
        $rule = new AndRule([new TrueRule()]);
        $rule->addRule(new TrueRule());

        static::assertEquals([new TrueRule(), new TrueRule()], $rule->getRules());

        $rule->setRules([new FalseRule()]);
        static::assertEquals([new FalseRule()], $rule->getRules());
    }

    public function testConstraintsAreStillTheSame(): void
    {
        static::assertEquals(
            ['rules' => [new ArrayOfType(Rule::class)]],
            (new AndRule())->getConstraints()
        );
    }

    public static function cases(): \Generator
    {
        yield 'Test with single matching rule' => [
            new AndRule([new TrueRule()]),
            true,
        ];

        yield 'Test with multiple matching rule' => [
            new AndRule([
                new TrueRule(),
                new TrueRule(),
            ]),
            true,
        ];

        yield 'Test with single not matching rule' => [
            new AndRule([new FalseRule()]),
            false,
        ];

        yield 'Test with multiple not matching rule' => [
            new AndRule([
                new TrueRule(),
                new FalseRule(),
            ]),
            false,
        ];

        yield 'Test with matching and not matching rule' => [
            new AndRule([
                new TrueRule(),
                new FalseRule(),
            ]),
            false,
        ];
    }
}