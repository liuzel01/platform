<?php declare(strict_types=1);

namespace Shuwei\Core\System\Locale;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\RestrictDelete;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Language\LanguageDefinition;
use Shuwei\Core\System\Locale\Aggregate\LocaleTranslation\LocaleTranslationDefinition;
use Shuwei\Core\System\User\UserDefinition;

#[Package('system-settings')]
class LocaleDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'locale';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return LocaleCollection::class;
    }

    public function getEntityClass(): string
    {
        return LocaleEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new StringField('code', 'code'))->addFlags(new ApiAware(), new Required(), new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new TranslatedField('name'))->addFlags(new ApiAware(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new TranslatedField('territory'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            (new OneToManyAssociationField('languages', LanguageDefinition::class, 'locale_id', 'id'))->addFlags(new CascadeDelete()),
            (new TranslationsAssociationField(LocaleTranslationDefinition::class, 'locale_id'))->addFlags(new Required()),

            // Reverse Associations not available in sales-channel-api
            (new OneToManyAssociationField('users', UserDefinition::class, 'locale_id', 'id'))->addFlags(new RestrictDelete()),
        ]);
    }
}
