<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Framework\Validation\Constraint\Uuid as UuidConstraint;

/**
 * @internal
 */
#[Package('core')]
class IdFieldSerializer extends AbstractFieldSerializer
{
    public function normalize(Field $field, array $data, WriteParameterBag $parameters): array
    {
        $key = $field->getPropertyName();
        if (!isset($data[$key])) {
            $data[$key] = Uuid::randomHex();
        }

        $parameters->getContext()->set($parameters->getDefinition()->getEntityName(), $key, $data[$key]);

        return $data;
    }

    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if (!$field instanceof IdField) {
            throw DataAbstractionLayerException::invalidSerializerField(IdField::class, $field);
        }

        $value = $data->getValue();
        if ($value) {
            $this->validate([new UuidConstraint()], $data, $parameters->getPath());
        } else {
            $value = Uuid::randomHex();
        }

        $parameters->getContext()->set($parameters->getDefinition()->getEntityName(), $data->getKey(), $value);

        yield $field->getStorageName() => Uuid::fromHexToBytes($value);
    }

    public function decode(Field $field, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Uuid::fromBytesToHex($value);
    }
}
