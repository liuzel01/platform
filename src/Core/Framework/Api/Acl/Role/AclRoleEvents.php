<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Acl\Role;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class AclRoleEvents
{
    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ACL_ROLE_WRITTEN_EVENT = 'acl_role.written';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ACL_ROLE_DELETED_EVENT = 'acl_role.deleted';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ACL_ROLE_LOADED_EVENT = 'acl_role.loaded';

    /**
     * @Event("Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ACL_ROLE_SEARCH_RESULT_LOADED_EVENT = 'acl_role.search.result.loaded';
}
