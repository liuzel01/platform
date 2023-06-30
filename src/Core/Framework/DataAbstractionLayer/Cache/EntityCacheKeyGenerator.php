<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Cache;

use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class EntityCacheKeyGenerator
{
    public function getCriteriaHash(Criteria $criteria): string
    {
        return md5((string) json_encode([
            $criteria->getIds(),
            $criteria->getFilters(),
            $criteria->getTerm(),
            $criteria->getPostFilters(),
            $criteria->getQueries(),
            $criteria->getSorting(),
            $criteria->getLimit(),
            $criteria->getOffset() ?? 0,
            $criteria->getTotalCountMode(),
            $criteria->getGroupFields(),
            $criteria->getAggregations(),
            $criteria->getAssociations(),
        ], \JSON_THROW_ON_ERROR));
    }
}
