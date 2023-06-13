<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Acl\Role;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<AclRoleEntity>
 */
#[Package('core')]
class AclRoleCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'dal_acl_role_collection';
    }

    protected function getExpectedClass(): string
    {
        return AclRoleEntity::class;
    }
}
