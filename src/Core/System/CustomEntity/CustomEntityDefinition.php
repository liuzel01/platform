<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityProtection\EntityProtectionCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityProtection\WriteProtection;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\PluginDefinition;

#[Package('core')]
class CustomEntityDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'custom_entity';

    public function getCollectionClass(): string
    {
        return CustomEntityCollection::class;
    }

    public function getEntityClass(): string
    {
        return CustomEntityEntity::class;
    }

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function since(): ?string
    {
        return '6.4.9.0';
    }

    protected function defineProtections(): EntityProtectionCollection
    {
        return new EntityProtectionCollection([
            new WriteProtection(Context::SYSTEM_SCOPE),
        ]);
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new JsonField('fields', 'fields'))->addFlags(new Required()),
            new JsonField('flags', 'flags'),
            new FkField('plugin_id', 'pluginId', PluginDefinition::class),
            (new BoolField('cms_aware', 'cmsAware'))->addFlags(new Runtime()),
            (new BoolField('store_api_aware', 'storeApiAware'))->addFlags(new Runtime()),
            new BoolField('custom_fields_aware', 'customFieldsAware'),
            new StringField('label_property', 'labelProperty'),
        ]);
    }
}
