<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Cache;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler(handles: InvalidateCacheTask::class)]
#[Package('core')]
final class InvalidateCacheTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly int $delay
    ) {
        parent::__construct($scheduledTaskRepository);
    }

    public function run(): void
    {
        try {
            if ($this->delay <= 0) {
                $this->cacheInvalidator->invalidateExpired(null);

                return;
            }

            $time = new \DateTime();
            $time->modify(sprintf('-%d second', $this->delay));
            $this->cacheInvalidator->invalidateExpired($time);
        } catch (\Throwable) {
        }
    }
}
