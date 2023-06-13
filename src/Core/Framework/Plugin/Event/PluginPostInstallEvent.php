<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Event;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Context\InstallContext;
use Shuwei\Core\Framework\Plugin\PluginEntity;

#[Package('core')]
class PluginPostInstallEvent extends PluginLifecycleEvent
{
    public function __construct(
        PluginEntity $plugin,
        private readonly InstallContext $context
    ) {
        parent::__construct($plugin);
    }

    public function getContext(): InstallContext
    {
        return $this->context;
    }
}
