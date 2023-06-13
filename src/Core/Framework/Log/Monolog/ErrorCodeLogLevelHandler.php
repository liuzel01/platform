<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Log\Monolog;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\LogRecord;
use Psr\Log\LogLevel;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class ErrorCodeLogLevelHandler extends AbstractHandler
{
    /**
     * @param array<string, value-of<Level::NAMES>|LogLevel::*|'Debug'|'Info'|'Notice'|'Warning'|'Error'|'Critical'|'Alert'|'Emergency'> $errorCodesToLogLevel
     *
     * @internal
     */
    public function __construct(
        private readonly HandlerInterface $handler,
        private readonly array $errorCodesToLogLevel
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(LogRecord $record): bool
    {
        if (
            isset($record->context['exception'])
            && \is_object($record->context['exception'])
            && $record->context['exception'] instanceof ShuweiHttpException
            && \array_key_exists($record->context['exception']->getErrorCode(), $this->errorCodesToLogLevel)
        ) {
            $level = Level::fromName($this->errorCodesToLogLevel[$record->context['exception']->getErrorCode()]);

            $record = new LogRecord(
                $record->datetime,
                $record->channel,
                $level,
                $record->message,
                $record->context,
                $record->extra,
                $record->formatted
            );
        }

        return $this->handler->handle($record);
    }
}
