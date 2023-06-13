<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Log;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\SearchRanking;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;

#[Package('core')]
class LogEntryDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'log_entry';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return LogEntryEntity::class;
    }

    public function getCollectionClass(): string
    {
        return LogEntryCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),

            (new LongTextField('message', 'message'))->addFlags(new SearchRanking(SearchRanking::HIGH_SEARCH_RANKING)),
            new IntField('level', 'level'),
            new StringField('channel', 'channel'),
            (new JsonField('context', 'context'))->addFlags(new SearchRanking(SearchRanking::MIDDLE_SEARCH_RANKING)),
            (new JsonField('extra', 'extra'))->addFlags(new SearchRanking(SearchRanking::LOW_SEARCH_RANKING)),
        ]);
    }
}
