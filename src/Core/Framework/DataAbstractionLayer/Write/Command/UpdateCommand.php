<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\Command;

use Shuwei\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shuwei\Core\Framework\Log\Package;

/**
 * @phpstan-ignore-next-line cannot be final, as it is extended, also designed to be used directly
 */
#[Package('core')]
class UpdateCommand extends WriteCommand implements ChangeSetAware
{
    use ChangeSetAwareTrait;

    public function getPrivilege(): ?string
    {
        return AclRoleDefinition::PRIVILEGE_UPDATE;
    }
}
