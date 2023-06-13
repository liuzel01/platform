<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class InvalidParentAssociationException extends ShuweiHttpException
{
    public function __construct(
        EntityDefinition $definition,
        Field $parentField
    ) {
        parent::__construct(
            'Parent property for {{ definition }} expected to be an ManyToOneAssociationField got {{ fieldDefinition }}',
            ['definition' => $definition->getEntityName(), 'fieldDefinition' => $parentField::class]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_PARENT_ASSOCIATION_EXCEPTION';
    }
}
