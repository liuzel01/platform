<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric;

use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResult;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class EntityResult extends AggregationResult
{
    /**
     * @param EntityCollection<Entity> $entities
     */
    public function __construct(
        string $name,
        protected EntityCollection $entities
    ) {
        parent::__construct($name);
    }

    /**
     * @return EntityCollection<Entity>
     */
    public function getEntities(): EntityCollection
    {
        return $this->entities;
    }

    public function add(Entity $entity): void
    {
        $this->entities->add($entity);
    }
}
