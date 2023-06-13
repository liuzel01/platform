<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Event\GenericEvent;
use Shuwei\Core\Framework\Event\NestedEvent;
use Shuwei\Core\Framework\Event\NestedEventCollection;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class EntityLoadedEvent extends NestedEvent implements GenericEvent
{
    /**
     * @var Entity[]
     */
    protected $entities;

    /**
     * @var EntityDefinition
     */
    protected $definition;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param Entity[] $entities
     */
    public function __construct(
        EntityDefinition $definition,
        array $entities,
        Context $context
    ) {
        $this->entities = $entities;
        $this->definition = $definition;
        $this->context = $context;
        $this->name = $this->definition->getEntityName() . '.loaded';
    }

    /**
     * @return Entity[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    public function getDefinition(): EntityDefinition
    {
        return $this->definition;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEvents(): ?NestedEventCollection
    {
        return null;
    }

    /**
     * @return list<string>
     */
    public function getIds(): array
    {
        $ids = [];

        foreach ($this->entities as $entity) {
            $ids[] = $entity->getUniqueIdentifier();
        }

        return $ids;
    }
}
