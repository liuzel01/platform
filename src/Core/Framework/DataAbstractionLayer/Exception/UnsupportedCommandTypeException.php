<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class UnsupportedCommandTypeException extends ShuweiHttpException
{
    public function __construct(WriteCommand $command)
    {
        parent::__construct(
            'Command of class {{ command }} is not supported by {{ definition }}',
            ['command' => $command::class, 'definition' => $command->getDefinition()->getEntityName()]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__UNSUPPORTED_COMMAND_TYPE_EXCEPTION';
    }
}
