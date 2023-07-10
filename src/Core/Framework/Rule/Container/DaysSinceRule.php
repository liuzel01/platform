<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule\Container;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Rule\Exception\UnsupportedValueException;
use Shuwei\Core\Framework\Rule\Rule;
use Shuwei\Core\Framework\Rule\RuleComparison;
use Shuwei\Core\Framework\Rule\RuleConfig;
use Shuwei\Core\Framework\Rule\RuleConstraints;
use Shuwei\Core\Framework\Rule\RuleScope;

#[Package('business-ops')]
abstract class DaysSinceRule extends Rule
{
    protected string $operator = Rule::OPERATOR_EQ;

    protected ?int $daysPassed = null;

    public function match(RuleScope $scope): bool
    {
        if (!$this->supportsScope($scope)) {
            return false;
        }

        $currentDate = $scope->getCurrentTime()->setTime(0, 0, 0, 0);

        if ($this->daysPassed === null && $this->operator !== self::OPERATOR_EMPTY) {
            throw new UnsupportedValueException(\gettype($this->daysPassed), self::class);
        }

        if (!$date = $this->getDate($scope)) {
            return RuleComparison::isNegativeOperator($this->operator);
        }

        if ($this->daysPassed === null) {
            return false;
        }

        if (method_exists($date, 'setTime')) {
            $date = $date->setTime(0, 0, 0, 0);
        }
        /** @var \DateInterval $interval */
        $interval = $date->diff($currentDate);

        if ($this->operator === self::OPERATOR_EMPTY) {
            return false;
        }

        return RuleComparison::numeric((int) $interval->days, $this->daysPassed, $this->operator);
    }

    public function getConstraints(): array
    {
        $constraints = [
            'operator' => RuleConstraints::numericOperators(),
        ];

        if ($this->operator === self::OPERATOR_EMPTY) {
            return $constraints;
        }

        $constraints['daysPassed'] = RuleConstraints::int();

        return $constraints;
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_NUMBER, true)
            ->intField('daysPassed');
    }

    abstract protected function getDate(RuleScope $scope): ?\DateTimeInterface;

    abstract protected function supportsScope(RuleScope $scope): bool;
}
