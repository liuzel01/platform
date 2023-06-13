<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\FieldCollection;

/**
 * @internal
 */
class DateDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = '_date_field_test';

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
            (new DateField('date', 'date'))->addFlags(new ApiAware(), new Required()),
            (new DateField('date_nullable', 'dateNullable'))->addFlags(new ApiAware()),
        ]);
    }
}
