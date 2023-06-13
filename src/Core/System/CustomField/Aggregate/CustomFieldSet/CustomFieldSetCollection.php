<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomField\Aggregate\CustomFieldSet;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<CustomFieldSetEntity>
 */
#[Package('system-settings')]
class CustomFieldSetCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'custom_field_set_collection';
    }

    protected function getExpectedClass(): string
    {
        return CustomFieldSetEntity::class;
    }
}
