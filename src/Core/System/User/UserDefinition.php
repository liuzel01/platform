<?php declare(strict_types=1);

namespace Shuwei\Core\System\User;

use Shuwei\Core\Checkout\Customer\CustomerDefinition;
use Shuwei\Core\Checkout\Order\OrderDefinition;
use Shuwei\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogDefinition;
use Shuwei\Core\Content\Media\MediaDefinition;
use Shuwei\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shuwei\Core\Framework\Api\Acl\Role\AclUserRoleDefinition;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityProtection\EntityProtectionCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityProtection\WriteProtection;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TimeZoneField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Locale\LocaleDefinition;
use Shuwei\Core\System\User\Aggregate\UserConfig\UserConfigDefinition;
use Shuwei\Core\System\User\Aggregate\UserRecovery\UserRecoveryDefinition;

#[Package('system-settings')]
class UserDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'user';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return UserCollection::class;
    }

    public function getEntityClass(): string
    {
        return UserEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    public function getDefaults(): array
    {
        return [
            'timeZone' => 'UTC',
        ];
    }

    protected function defineProtections(): EntityProtectionCollection
    {
        return new EntityProtectionCollection([new WriteProtection(Context::SYSTEM_SCOPE)]);
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('locale_id', 'localeId', LocaleDefinition::class))->addFlags(new Required()),
            (new StringField('username', 'username'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new PasswordField('password', 'password', \PASSWORD_DEFAULT, [], PasswordField::FOR_ADMIN))->removeFlag(ApiAware::class)->addFlags(new Required()),
            (new StringField('name', 'firstName'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new StringField('title', 'title'))->addFlags(new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new StringField('email', 'email'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            new BoolField('active', 'active'),
            new BoolField('admin', 'admin'),
            new DateTimeField('last_updated_password_at', 'lastUpdatedPasswordAt'),
            (new TimeZoneField('time_zone', 'timeZone'))->addFlags(new Required()),
            new CustomFields(),
            new ManyToOneAssociationField('locale', 'locale_id', LocaleDefinition::class, 'id', false),
            (new OneToManyAssociationField('configs', UserConfigDefinition::class, 'user_id', 'id'))->addFlags(new CascadeDelete()),
            new ManyToManyAssociationField('aclRoles', AclRoleDefinition::class, AclUserRoleDefinition::class, 'user_id', 'acl_role_id'),
            new OneToOneAssociationField('recoveryUser', 'id', 'user_id', UserRecoveryDefinition::class, false),
            (new StringField('store_token', 'storeToken'))->removeFlag(ApiAware::class),
        ]);
    }
}
