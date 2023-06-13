<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\MessageQueue\fixtures;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskDefinition;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskEntity;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler(handles: TestTask::class)]
final class DummyScheduledTaskHandler extends ScheduledTaskHandler
{
    private bool $wasCalled = false;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private readonly string $taskId,
        private readonly bool $shouldThrowException = false
    ) {
        parent::__construct($scheduledTaskRepository);
    }

    public function run(): void
    {
        $this->wasCalled = true;

        /** @var ScheduledTaskEntity $task */
        $task = $this->scheduledTaskRepository
            ->search(new Criteria([$this->taskId]), Context::createDefaultContext())
            ->get($this->taskId);

        if ($task->getStatus() !== ScheduledTaskDefinition::STATUS_RUNNING) {
            throw new \Exception('Scheduled Task was not marked as running.');
        }

        if ($this->shouldThrowException) {
            throw new \RuntimeException('This Exception should be thrown');
        }
    }

    public function wasCalled(): bool
    {
        return $this->wasCalled;
    }
}
