<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Aggregate\MediaTranslation;

use Shuwei\Core\Content\Media\MediaDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;

#[Package('content')]
class MediaTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = 'media_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return MediaTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return MediaTranslationEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return MediaDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new ApiAware()),
            (new LongTextField('alt', 'alt'))->addFlags(new ApiAware()),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}
