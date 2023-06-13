<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Acl\Role;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\User\UserDefinition;

#[Package('core')]
class AclUserRoleDefinition extends MappingEntityDefinition
{
    final public const ENTITY_NAME = 'acl_user_role';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('user_id', 'userId', UserDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('acl_role_id', 'aclRoleId', AclRoleDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new CreatedAtField(),
            new UpdatedAtField(),
            new ManyToOneAssociationField('user', 'user_id', UserDefinition::class),
            new ManyToOneAssociationField('aclRole', 'acl_role_id', AclRoleDefinition::class),
        ]);
    }
}
