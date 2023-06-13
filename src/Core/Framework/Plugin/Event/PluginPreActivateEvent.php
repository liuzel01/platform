<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Event;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Context\ActivateContext;
use Shuwei\Core\Framework\Plugin\PluginEntity;

#[Package('core')]
class PluginPreActivateEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly ActivateContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): ActivateContext
    {
        return $this->context;
    }
}
