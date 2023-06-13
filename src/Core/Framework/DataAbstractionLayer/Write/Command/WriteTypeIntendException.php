<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\Command;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class WriteTypeIntendException extends ShuweiHttpException
{
    public function __construct(
        EntityDefinition $definition,
        string $expectedClass,
        string $actualClass
    ) {
        parent::__construct(
            'Expected command for "{{ definition }}" to be "{{ expectedClass }}". (Got: {{ actualClass }})',
            ['definition' => $definition->getEntityName(), 'expectedClass' => $expectedClass, 'actualClass' => $actualClass]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__WRITE_TYPE_INTEND_ERROR';
    }
}
