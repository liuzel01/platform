<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule\Container;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Rule\Exception\UnsupportedValueException;
use Shuwei\Core\Framework\Rule\Rule;
use Shuwei\Core\Framework\Rule\RuleScope;

#[Package('business-ops
NotRule inverses the return value of the child rule. Only one child is possible')]
class NotRule extends Container
{
    final public const RULE_NAME = 'notContainer';

    public function addRule(Rule $rule): void
    {
        parent::addRule($rule);
        $this->checkRules();
    }

    public function setRules(array $rules): void
    {
        parent::setRules(array_values($rules));
        $this->checkRules();
    }

    public function match(RuleScope $scope): bool
    {
        $rules = $this->rules;

        $rule = array_shift($rules);

        if (!$rule instanceof Rule) {
            throw new UnsupportedValueException(\gettype($rule), self::class);
        }

        return !$rule->match($scope);
    }

    /**
     * Enforce that NOT only handles ONE child rule
     *
     * @throws \RuntimeException
     */
    protected function checkRules(): void
    {
        if (\count($this->rules) > 1) {
            throw new \RuntimeException('NOT rule can only hold one rule');
        }
    }
}
