<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\Validation;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shuwei\Core\Framework\Event\ShuweiEvent;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class WriteCommandExceptionEvent extends Event implements ShuweiEvent
{
    /**
     * @param WriteCommand[] $commands
     */
    public function __construct(
        private readonly \Throwable $exception,
        private readonly array $commands,
        private readonly Context $context
    ) {
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
