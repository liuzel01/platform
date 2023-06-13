<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\AllowEmptyString;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\Snippet\Aggregate\SnippetSet\SnippetSetDefinition;

#[Package('system-settings')]
class SnippetDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'snippet';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return SnippetCollection::class;
    }

    public function getEntityClass(): string
    {
        return SnippetEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): ?string
    {
        return SnippetSetDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('snippet_set_id', 'setId', SnippetSetDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new StringField('translation_key', 'translationKey'))->addFlags(new ApiAware(), new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new LongTextField('value', 'value'))->addFlags(new ApiAware(), new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING), new AllowHtml(), new AllowEmptyString()),
            (new StringField('author', 'author'))->addFlags(new Required(), new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            (new CustomFields())->addFlags(new ApiAware()),
            new ManyToOneAssociationField('set', 'snippet_set_id', SnippetSetDefinition::class, 'id', false),
        ]);
    }
}
