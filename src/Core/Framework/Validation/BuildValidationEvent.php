<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Validation;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Event\GenericEvent;
use Shuwei\Core\Framework\Event\ShuweiEvent;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Validation\DataBag\DataBag;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class BuildValidationEvent extends Event implements ShuweiEvent, GenericEvent
{
    public function __construct(
        private readonly DataValidationDefinition $definition,
        private readonly DataBag $data,
        private readonly Context $context
    ) {
    }

    public function getName(): string
    {
        return 'framework.validation.' . $this->definition->getName();
    }

    public function getDefinition(): DataValidationDefinition
    {
        return $this->definition;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getData(): DataBag
    {
        return $this->data;
    }
}
