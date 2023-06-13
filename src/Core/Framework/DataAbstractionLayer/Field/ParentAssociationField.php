<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class ParentAssociationField extends ManyToOneAssociationField
{
    public function __construct(
        string $referenceClass,
        string $referenceField = 'id'
    ) {
        parent::__construct('parent', 'parent_id', $referenceClass, $referenceField, false);
    }
}
