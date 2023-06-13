<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Event;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Context\UninstallContext;
use Shuwei\Core\Framework\Plugin\PluginEntity;

#[Package('core')]
class PluginPreUninstallEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly UninstallContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): UninstallContext
    {
        return $this->context;
    }
}
