<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Aggregate\MediaDefaultFolder;

use Shuwei\Core\Content\Media\Aggregate\MediaFolder\MediaFolderDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ListField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class MediaDefaultFolderDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'media_default_folder';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MediaDefaultFolderCollection::class;
    }

    public function getEntityClass(): string
    {
        return MediaDefaultFolderEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        $fields = new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new StringField('entity', 'entity'))->addFlags(new Required()),
            new OneToOneAssociationField('folder', 'id', 'default_folder_id', MediaFolderDefinition::class),
            new CustomFields(),
        ]);

        if (!Feature::isActive('v6.6.0.0')) {
            /**
             * @deprecated tag:v6.6.0 Associations are now determined by `\Shuwei\Core\Content\Media\MediaDefinition`
             */
            $fields->add(
                (new ListField('association_fields', 'associationFields', StringField::class))->addFlags(new Required())
            );
        }

        return $fields;
    }
}
