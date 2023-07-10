<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule\Container;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
class OrRule extends Container
{
    final public const RULE_NAME = 'orContainer';

    public function match(RuleScope $scope): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->match($scope)) {
                return true;
            }
        }

        return false;
    }
}
