<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
interface EntityAggregatorInterface
{
    public function aggregate(EntityDefinition $definition, Criteria $criteria, Context $context): AggregationResultCollection;
}
