<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Framework\Validation\Constraint\Uuid as UuidConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @internal
 */
#[Package('core')]
class FkFieldSerializer extends AbstractFieldSerializer
{
    public function normalize(Field $field, array $data, WriteParameterBag $parameters): array
    {
        if (!$field instanceof FkField) {
            throw DataAbstractionLayerException::invalidSerializerField(FkField::class, $field);
        }

        $value = $data[$field->getPropertyName()] ?? null;

        $writeContext = $parameters->getContext();

        if ($this->shouldUseContext($field, true, $value) && $writeContext->has($field->getReferenceDefinition()->getEntityName(), $field->getReferenceField())) {
            $data[$field->getPropertyName()] = $writeContext->get($field->getReferenceDefinition()->getEntityName(), $field->getReferenceField());
        }

        return $data;
    }

    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if (!$field instanceof FkField) {
            throw DataAbstractionLayerException::invalidSerializerField(FkField::class, $field);
        }

        $value = $data->getValue();

        if ($this->shouldUseContext($field, $data->isRaw(), $value)) {
            try {
                $value = $parameters->getContext()->get($field->getReferenceDefinition()->getEntityName(), $field->getReferenceField());
            } catch (\InvalidArgumentException) {
                if ($this->requiresValidation($field, $existence, $value, $parameters)) {
                    $this->validate($this->getConstraints($field), $data, $parameters->getPath());
                }
            }
        }

        if ($value === null) {
            yield $field->getStorageName() => null;

            return;
        }
        if ($this->requiresValidation($field, $existence, $value, $parameters)) {
            $this->validate([new UuidConstraint()], $data, $parameters->getPath());
        }

        if ($value !== null) {
            $value = Uuid::fromHexToBytes($value);
        }

        yield $field->getStorageName() => $value;
    }

    public function decode(Field $field, mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return Uuid::fromBytesToHex($value);
    }

    /**
     * @deprecated tag:v6.6.0 - reason:return-type-change - Parameter $value will be natively typed as mixed
     *
     * @param mixed $value
     */
    protected function shouldUseContext(FkField $field, bool $isRaw, $value): bool
    {
        return $isRaw && $value === null && $field->is(Required::class);
    }

    protected function getConstraints(Field $field): array
    {
        return [new NotBlank()];
    }
}
