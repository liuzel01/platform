<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shuwei\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition\NamedOptionalGroupDefinition;

/**
 * @internal
 */
class NamedDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'named';

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
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new ApiAware(), new Required()),
            new FkField('optional_group_id', 'optionalGroupId', NamedOptionalGroupDefinition::class),
            new ManyToOneAssociationField('optionalGroup', 'optional_group_id', NamedOptionalGroupDefinition::class, 'id', true),
        ]);
    }
}
