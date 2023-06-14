<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BlobField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\Aggregate\PluginTranslation\PluginTranslationDefinition;

#[Package('core')]
class PluginDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'plugin';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return PluginCollection::class;
    }

    public function getEntityClass(): string
    {
        return PluginEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('base_class', 'baseClass'))->addFlags(new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            new StringField('composer_name', 'composerName'),
            (new JsonField('autoload', 'autoload'))->addFlags(new Required()),
            new BoolField('active', 'active'),
            new BoolField('managed_by_composer', 'managedByComposer'),
            new StringField('path', 'path'),
            new StringField('author', 'author'),
            new StringField('copyright', 'copyright'),
            new StringField('license', 'license'),
            (new StringField('version', 'version'))->addFlags(new Required()),
            new StringField('upgrade_version', 'upgradeVersion'),
            new DateTimeField('installed_at', 'installedAt'),
            new DateTimeField('upgraded_at', 'upgradedAt'),
            (new BlobField('icon', 'iconRaw'))->removeFlag(ApiAware::class),
            (new StringField('icon', 'icon'))->addFlags(new WriteProtected(), new Runtime()),
            new TranslatedField('label'),
            new TranslatedField('description'),
            new TranslatedField('manufacturerLink'),
            new TranslatedField('supportLink'),
            new TranslatedField('changelog'),
            new TranslatedField('customFields'),
            (new TranslationsAssociationField(PluginTranslationDefinition::class, 'plugin_id'))->addFlags(new Required(), new CascadeDelete()),
        ]);
    }
}
