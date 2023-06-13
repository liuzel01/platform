<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Collection;
use Shuwei\Core\System\SalesChannel\Entity\PartialSalesChannelEntityLoadedEvent;
use Shuwei\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shuwei\Core\System\SalesChannel\SalesChannelContext;

/**
 * @internal
 */
#[Package('core')]
class EntityLoadedEventFactory
{
    public function __construct(private readonly DefinitionInstanceRegistry $registry)
    {
    }

    /**
     * @param list<mixed> $entities
     */
    public function create(array $entities, Context $context): EntityLoadedContainerEvent
    {
        $mapping = $this->recursion($entities, []);

        $generator = fn (EntityDefinition $definition, array $entities) => new EntityLoadedEvent($definition, $entities, $context);

        return $this->buildEvents($mapping, $generator, $context);
    }

    /**
     * @param list<mixed> $entities
     */
    public function createPartial(array $entities, Context $context): EntityLoadedContainerEvent
    {
        $mapping = $this->recursion($entities, []);

        $generator = fn (EntityDefinition $definition, array $entities) => new PartialEntityLoadedEvent($definition, $entities, $context);

        return $this->buildEvents($mapping, $generator, $context);
    }

    /**
     * @param list<mixed> $entities
     *
     * @return EntityLoadedContainerEvent[]
     */
    public function createForSalesChannel(array $entities, SalesChannelContext $context): array
    {
        $mapping = $this->recursion($entities, []);

        $generator = fn (EntityDefinition $definition, array $entities) => new EntityLoadedEvent($definition, $entities, $context->getContext());

        $salesGenerator = fn (EntityDefinition $definition, array $entities) => new SalesChannelEntityLoadedEvent($definition, $entities, $context);

        return [
            $this->buildEvents($mapping, $generator, $context->getContext()),
            $this->buildEvents($mapping, $salesGenerator, $context->getContext()),
        ];
    }

    /**
     * @param list<mixed> $entities
     *
     * @return EntityLoadedContainerEvent[]
     */
    public function createPartialForSalesChannel(array $entities, SalesChannelContext $context): array
    {
        $mapping = $this->recursion($entities, []);

        $generator = fn (EntityDefinition $definition, array $entities) => new PartialEntityLoadedEvent($definition, $entities, $context->getContext());

        $salesGenerator = fn (EntityDefinition $definition, array $entities) => new PartialSalesChannelEntityLoadedEvent($definition, $entities, $context);

        return [
            $this->buildEvents($mapping, $generator, $context->getContext()),
            $this->buildEvents($mapping, $salesGenerator, $context->getContext()),
        ];
    }

    /**
     * @param array<string, list<Entity>> $mapping
     */
    private function buildEvents(array $mapping, \Closure $generator, Context $context): EntityLoadedContainerEvent
    {
        $events = [];
        foreach ($mapping as $name => $entities) {
            $definition = $this->registry->getByEntityName($name);

            $events[] = $generator($definition, $entities);
        }

        return new EntityLoadedContainerEvent($context, $events);
    }

    /**
     * @param list<mixed> $entities
     * @param array<string, list<Entity>> $mapping
     *
     * @return array<string, list<Entity>>
     */
    private function recursion(array $entities, array $mapping): array
    {
        foreach ($entities as $entity) {
            if (!$entity instanceof Entity && !$entity instanceof EntityCollection) {
                continue;
            }

            if ($entity instanceof EntityCollection) {
                $mapping = $this->recursion($entity->getElements(), $mapping);
            } else {
                $mapping = $this->map($entity, $mapping);
            }
        }

        return $mapping;
    }

    /**
     * @param array<string, list<Entity>> $mapping
     *
     * @return array<string, list<Entity>>
     */
    private function map(Entity $entity, array $mapping): array
    {
        $mapping[$entity->getInternalEntityName()][] = $entity;

        $vars = $entity->getVars();
        foreach ($vars as $value) {
            if ($value instanceof Entity) {
                $mapping = $this->map($value, $mapping);

                continue;
            }

            if ($value instanceof Collection) {
                $value = $value->getElements();
            }
            if (!\is_array($value)) {
                continue;
            }

            $mapping = $this->recursion($value, $mapping);
        }

        return $mapping;
    }
}
