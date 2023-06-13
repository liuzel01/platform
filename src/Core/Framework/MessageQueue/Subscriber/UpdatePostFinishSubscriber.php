<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\MessageQueue\Subscriber;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\Registry\TaskRegistry;
use Shuwei\Core\Framework\Update\Event\UpdatePostFinishEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
#[Package('system-settings')]
final class UpdatePostFinishSubscriber implements EventSubscriberInterface
{
    /**
     * @internal
     */
    public function __construct(private readonly TaskRegistry $registry)
    {
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [UpdatePostFinishEvent::class => 'updatePostFinishEvent'];
    }

    public function updatePostFinishEvent(): void
    {
        $this->registry->registerTasks();
    }
}
