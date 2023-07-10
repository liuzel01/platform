<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule\Container;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Rule\Rule;

#[Package('business-ops')]
interface ContainerInterface
{
    /**
     * @param Rule[] $rules
     */
    public function setRules(array $rules): void;

    public function addRule(Rule $rule): void;
}
