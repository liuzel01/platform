<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\Command;

use Shuwei\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class InsertCommand extends WriteCommand
{
    public function getPrivilege(): ?string
    {
        return AclRoleDefinition::PRIVILEGE_CREATE;
    }
}
