<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Event\ShuweiEvent;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class EntitySearchedEvent extends Event implements ShuweiEvent
{
    public function __construct(
        private readonly Criteria $criteria,
        private readonly EntityDefinition $definition,
        private readonly Context $context
    ) {
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function getDefinition(): EntityDefinition
    {
        return $this->definition;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
