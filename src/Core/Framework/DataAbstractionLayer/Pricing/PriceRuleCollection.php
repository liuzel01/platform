<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Pricing;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<PriceRuleEntity>
 */
#[Package('core')]
class PriceRuleCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'dal_price_rule_collection';
    }

    protected function getExpectedClass(): string
    {
        return PriceRuleEntity::class;
    }
}
