<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\DataAbstractionLayer\Write\Validation\TestDefinition;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\LockedField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;

/**
 * @internal
 */
class TestDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = '_test_lock';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new StringField('description', 'description'))->addFlags(new ApiAware()),
            (new TranslatedField('name'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(TestTranslationDefinition::class, '_test_lock_id'))->addFlags(new ApiAware()),
            (new LockedField())->addFlags(new ApiAware()),
        ]);
    }
}
