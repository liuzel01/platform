<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\MessageQueue\Subscriber;

use Psr\Cache\CacheItemPoolInterface;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\Registry\TaskRegistry;
use Shuwei\Core\Framework\Plugin\Event\PluginPostActivateEvent;
use Shuwei\Core\Framework\Plugin\Event\PluginPostDeactivateEvent;
use Shuwei\Core\Framework\Plugin\Event\PluginPostUpdateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\EventListener\StopWorkerOnRestartSignalListener;

/**
 * @internal
 */
#[Package('system-settings')]
final class PluginLifecycleSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly TaskRegistry $registry,
        private readonly CacheItemPoolInterface $restartSignalCachePool
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginPostActivateEvent::class => 'afterPluginStateChange',
            PluginPostDeactivateEvent::class => 'afterPluginStateChange',
            PluginPostUpdateEvent::class => 'afterPluginStateChange',
        ];
    }

    public function afterPluginStateChange(): void
    {
        $this->registry->registerTasks();

        // signal worker restart
        $cacheItem = $this->restartSignalCachePool->getItem(StopWorkerOnRestartSignalListener::RESTART_REQUESTED_TIMESTAMP_KEY);
        $cacheItem->set(microtime(true));
        $this->restartSignalCachePool->save($cacheItem);
    }
}
