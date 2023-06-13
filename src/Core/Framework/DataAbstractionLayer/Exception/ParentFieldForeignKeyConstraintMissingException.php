<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class ParentFieldForeignKeyConstraintMissingException extends ShuweiHttpException
{
    public function __construct(
        EntityDefinition $definition,
        Field $parentField
    ) {
        parent::__construct(
            'Foreign key property {{ propertyName }} of parent association in definition {{ definition }} expected to be an FkField got %s',
            [
                'definition' => $definition->getEntityName(),
                'propertyName' => $parentField->getPropertyName(),
                'propertyClass' => $parentField::class,
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PARENT_FIELD_KEY_CONSTRAINT_MISSING';
    }
}
