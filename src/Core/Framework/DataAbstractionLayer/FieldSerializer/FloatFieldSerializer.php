<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @internal
 */
#[Package('core')]
class FloatFieldSerializer extends AbstractFieldSerializer
{
    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        if (!$field instanceof FloatField) {
            throw DataAbstractionLayerException::invalidSerializerField(FloatField::class, $field);
        }

        $this->validateIfNeeded($field, $existence, $data, $parameters);

        if ($data->getValue() === null) {
            yield $field->getStorageName() => null;

            return;
        }

        yield $field->getStorageName() => (float) $data->getValue();
    }

    public function decode(Field $field, mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }

    protected function getConstraints(Field $field): array
    {
        return [
            new NotBlank(),
            new Type('numeric'),
        ];
    }
}
