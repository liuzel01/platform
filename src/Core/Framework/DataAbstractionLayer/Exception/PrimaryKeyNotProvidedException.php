<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class PrimaryKeyNotProvidedException extends ShuweiHttpException
{
    public function __construct(
        EntityDefinition $definition,
        Field $field
    ) {
        parent::__construct(
            'Expected primary key field {{ propertyName }} for definition {{ definition }} not provided',
            ['definition' => $definition->getEntityName(), 'propertyName' => $field->getPropertyName()]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PRIMARY_KEY_NOT_PROVIDED';
    }
}
