<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class CanNotFindParentStorageFieldException extends ShuweiHttpException
{
    public function __construct(EntityDefinition $definition)
    {
        parent::__construct(
            'Can not find FkField for parent property definition {{ definition }}',
            ['definition' => $definition->getEntityName()]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__CAN_NOT_FIND_PARENT_STORAGE_FIELD';
    }
}
