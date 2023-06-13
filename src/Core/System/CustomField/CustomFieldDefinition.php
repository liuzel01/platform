<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomField;

use Shuwei\Core\Content\Product\Aggregate\ProductSearchConfigField\ProductSearchConfigFieldDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetDefinition;

#[Package('system-settings')]
class CustomFieldDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'custom_field';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CustomFieldCollection::class;
    }

    public function getEntityClass(): string
    {
        return CustomFieldEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    public function getDefaults(): array
    {
        return [
            'allowCustomerWrites' => false,
            'allowCartExpose' => false,
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new StringField('type', 'type'))->addFlags(new Required()),
            new JsonField('config', 'config', [], []),
            new BoolField('active', 'active'),
            new FkField('set_id', 'customFieldSetId', CustomFieldSetDefinition::class),
            new BoolField('allow_customer_write', 'allowCustomerWrite'),
            new BoolField('allow_cart_expose', 'allowCartExpose'),
            new ManyToOneAssociationField('customFieldSet', 'set_id', CustomFieldSetDefinition::class, 'id', false),
            (new OneToManyAssociationField('productSearchConfigFields', ProductSearchConfigFieldDefinition::class, 'custom_field_id', 'id'))->addFlags(new CascadeDelete()),
        ]);
    }
}
