<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule\Container;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Rule\RuleScope;

#[Package('business-ops
AndRule returns true, if all child-rules are true')]
class AndRule extends Container
{
    final public const RULE_NAME = 'andContainer';

    public function match(RuleScope $scope): bool
    {
        foreach ($this->rules as $rule) {
            $match = $rule->match($scope);

            if (!$match) {
                return false;
            }
        }

        return true;
    }
}
