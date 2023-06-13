<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Event\NestedEvent;
use Shuwei\Core\Framework\Event\NestedEventCollection;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class EntityLoadedContainerEvent extends NestedEvent
{
    public function __construct(
        private readonly Context $context,
        private readonly array $events
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getEvents(): ?NestedEventCollection
    {
        return new NestedEventCollection($this->events);
    }
}
