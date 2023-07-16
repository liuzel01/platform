<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Rule;

use Shuwei\Core\Framework\Log\Package;
use Frontend\Website\WebsiteDefinition;

#[Package('business-ops')]
class WebsiteRule extends Rule
{
    final public const RULE_NAME = 'website';

    /**
     * @internal
     *
     * @param list<string>|null $websiteIds
     */
    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?array $websiteIds = null
    ) {
        parent::__construct();
    }

    public function match(RuleScope $scope): bool
    {
        return RuleComparison::uuids([$scope->getFrontendContext()->getWebsite()->getId()], $this->websiteIds, $this->operator);
    }

    public function getConstraints(): array
    {
        return [
            'websiteIds' => RuleConstraints::uuids(),
            'operator' => RuleConstraints::uuidOperators(false),
        ];
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->entitySelectField('websiteIds', WebsiteDefinition::ENTITY_NAME, true);
    }
}
