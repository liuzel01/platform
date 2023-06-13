<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shuwei\Core\Framework\Event\GenericEvent;
use Shuwei\Core\Framework\Event\NestedEvent;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class EntitySearchResultLoadedEvent extends NestedEvent implements GenericEvent
{
    /**
     * @var EntitySearchResult
     */
    protected $result;

    /**
     * @var EntityDefinition
     */
    protected $definition;

    /**
     * @var string
     */
    protected $name;

    public function __construct(
        EntityDefinition $definition,
        EntitySearchResult $result
    ) {
        $this->result = $result;
        $this->definition = $definition;
        $this->name = $this->definition->getEntityName() . '.search.result.loaded';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContext(): Context
    {
        return $this->result->getContext();
    }

    public function getResult(): EntitySearchResult
    {
        return $this->result;
    }
}
