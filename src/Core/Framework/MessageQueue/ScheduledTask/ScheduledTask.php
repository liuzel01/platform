<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\MessageQueue\ScheduledTask;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\AsyncMessageInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Package('core')]
abstract class ScheduledTask implements AsyncMessageInterface
{
    protected ?string $taskId = null;

    /**
     * @internal
     */
    final public function __construct()
    {
        // needs to be empty
    }

    public function getTaskId(): ?string
    {
        return $this->taskId;
    }

    public function setTaskId(string $taskId): void
    {
        $this->taskId = $taskId;
    }

    abstract public static function getTaskName(): string;

    /**
     * @return int the default interval this task should run in seconds
     */
    abstract public static function getDefaultInterval(): int;

    public static function shouldRun(ParameterBagInterface $bag): bool
    {
        return true;
    }
}
