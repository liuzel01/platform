<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Collection;

/**
 * @extends Collection<Field>
 */
#[Package('core')]
class FieldCollection extends Collection
{
    public function compile(DefinitionInstanceRegistry $registry): CompiledFieldCollection
    {
        /** @var Field $field */
        foreach ($this->elements as $field) {
            $field->compile($registry);
        }

        return new CompiledFieldCollection($registry, $this->elements);
    }

    public function getApiAlias(): string
    {
        return 'dal_field_collection';
    }

    protected function getExpectedClass(): ?string
    {
        return Field::class;
    }
}
