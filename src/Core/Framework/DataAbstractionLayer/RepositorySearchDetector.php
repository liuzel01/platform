<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\CriteriaPartInterface;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class RepositorySearchDetector
{
    public static function isSearchRequired(EntityDefinition $definition, Criteria $criteria): bool
    {
        // total counts can only be fetched by entity searcher
        if ($criteria->getTotalCountMode() !== Criteria::TOTAL_COUNT_MODE_NONE) {
            return true;
        }

        if ($criteria->getTerm()) {
            return true;
        }

        // paginated lists are handled by entity searcher
        if ($criteria->getOffset() !== null || $criteria->getLimit() !== null) {
            return true;
        }

        // group by is only supported by entity searcher
        if (\count($criteria->getGroupFields())) {
            return true;
        }

        // sortings are only supported by entity searcher
        if (\count($criteria->getSorting())) {
            return true;
        }

        // queries can only be handled in entity searcher
        if (\count($criteria->getQueries())) {
            return true;
        }

        $filters = array_merge(
            $criteria->getFilters(),
            $criteria->getPostFilters()
        );

        /** @var CriteriaPartInterface $filter */
        foreach ($filters as $filter) {
            $accessors = $filter->getFields();
            foreach ($accessors as $accessor) {
                // get all fields of accessor to check which fields will be joined
                $definitionFields = EntityDefinitionQueryHelper::getFieldsOfAccessor($definition, $accessor);

                // if the criteria contains an access to a OneToMany or ManyToMany field, the query would run into a temporary table.
                // to prevent overload for sql servers we will execute a minimal query with the ids at first
                foreach ($definitionFields as $field) {
                    if ($field instanceof ManyToManyAssociationField || $field instanceof OneToManyAssociationField) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
