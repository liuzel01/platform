<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Event;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\PluginEntity;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
abstract class PluginLifecycleEvent extends Event
{
    public function __construct(private readonly PluginEntity $plugin)
    {
    }

    public function getPlugin(): PluginEntity
    {
        return $this->plugin;
    }
}
