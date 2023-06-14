<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomField\Aggregate\CustomFieldSet;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ReverseInherited;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomField\Aggregate\CustomFieldSetRelation\CustomFieldSetRelationDefinition;
use Shuwei\Core\System\CustomField\CustomFieldDefinition;

#[Package('system-settings')]
class CustomFieldSetDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'custom_field_set';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return CustomFieldSetCollection::class;
    }

    public function getEntityClass(): string
    {
        return CustomFieldSetEntity::class;
    }

    public function getDefaults(): array
    {
        return ['position' => 1];
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            new JsonField('config', 'config', [], []),
            new BoolField('active', 'active'),
            new BoolField('global', 'global'),
            new IntField('position', 'position'),
            (new OneToManyAssociationField('customFields', CustomFieldDefinition::class, 'set_id'))->addFlags(new CascadeDelete()),
            (new OneToManyAssociationField('relations', CustomFieldSetRelationDefinition::class, 'set_id'))->addFlags(new CascadeDelete()),
        ]);
    }
}
