<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Struct\FrwState;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @internal
 */
#[Package('merchant-services')]
class FirstRunWizardStartedEvent extends Event
{
    public function __construct(
        private readonly FrwState $state,
        private readonly Context $context
    ) {
    }

    public function getState(): FrwState
    {
        return $this->state;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
