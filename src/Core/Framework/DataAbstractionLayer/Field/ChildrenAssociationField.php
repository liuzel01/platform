<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class ChildrenAssociationField extends OneToManyAssociationField
{
    public function __construct(
        string $referenceClass,
        string $propertyName = 'children'
    ) {
        parent::__construct($propertyName, $referenceClass, 'parent_id');
        $this->addFlags(new CascadeDelete());
    }
}
