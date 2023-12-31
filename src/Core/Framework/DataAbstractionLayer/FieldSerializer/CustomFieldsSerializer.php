<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\Command\JsonUpdateCommand;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\Json;
use Shuwei\Core\System\CustomField\CustomFieldService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
#[Package('core')]
class CustomFieldsSerializer extends JsonFieldSerializer
{
    public function __construct(
        DefinitionInstanceRegistry $definitionRegistry,
        ValidatorInterface $validator,
        private readonly CustomFieldService $attributeService
    ) {
        parent::__construct($validator, $definitionRegistry);
    }

    public function encode(Field $field, EntityExistence $existence, KeyValuePair $data, WriteParameterBag $parameters): \Generator
    {
        if (!$field instanceof CustomFields) {
            throw DataAbstractionLayerException::invalidSerializerField(CustomFields::class, $field);
        }

        $this->validateIfNeeded($field, $existence, $data, $parameters);
        /** @var array<string, mixed> $attributes */
        $attributes = $data->getValue();
        if ($attributes === null) {
            yield $field->getStorageName() => null;

            return;
        }

        if (empty($attributes)) {
            yield $field->getStorageName() => '{}';

            return;
        }

        // set fields dynamically
        $field->setPropertyMapping($this->getFields(array_keys($attributes)));
        $encoded = $this->validateMapping($field, $attributes, $parameters);

        if (empty($encoded)) {
            return;
        }

        if ($existence->exists()) {
            $this->extractJsonUpdate([$field->getStorageName() => $encoded], $existence, $parameters);

            return;
        }

        yield $field->getStorageName() => Json::encode($encoded);
    }

    /**
     * @return array<string, mixed>|object|null
     */
    public function decode(Field $field, mixed $value): array|object|null
    {
        if (!$field instanceof CustomFields) {
            throw DataAbstractionLayerException::invalidSerializerField(CustomFields::class, $field);
        }

        if ($value) {
            // set fields dynamically
            /** @var array<string> $attributes */
            $attributes = array_keys(json_decode((string) $value, true, 512, \JSON_THROW_ON_ERROR));

            $field->setPropertyMapping($this->getFields($attributes));
        }

        return parent::decode($field, $value);
    }

    /**
     * @param array<string> $attributeNames
     *
     * @return array<Field>
     */
    private function getFields(array $attributeNames): array
    {
        $fields = [];
        foreach ($attributeNames as $attributeName) {
            $fields[] = $this->attributeService->getCustomField($attributeName)
                ?? new JsonField($attributeName, $attributeName);
        }

        return $fields;
    }

    /**
     * @param array<string, array<string, mixed>> $data
     */
    private function extractJsonUpdate(array $data, EntityExistence $existence, WriteParameterBag $parameters): void
    {
        foreach ($data as $storageName => $attributes) {
            $entityName = $existence->getEntityName();
            if (!$entityName) {
                continue;
            }

            $definition = $this->definitionRegistry->getByEntityName($entityName);

            $pks = array_combine(
                array_keys($existence->getPrimaryKey()),
                array_map(
                    function (string $pkFieldStorageName) use ($definition, $existence, $parameters): mixed {
                        $pkFieldValue = $existence->getPrimaryKey()[$pkFieldStorageName];
                        /** @var Field|null $field */
                        $field = $definition->getFields()->getByStorageName($pkFieldStorageName);
                        if (!$field) {
                            return $pkFieldValue;
                        }

                        return $field->getSerializer()->encode(
                            $field,
                            $existence,
                            new KeyValuePair($field->getPropertyName(), $pkFieldValue, true),
                            $parameters,
                        )->current();
                    },
                    array_keys($existence->getPrimaryKey()),
                ),
            );

            $jsonUpdateCommand = new JsonUpdateCommand(
                $definition,
                $storageName,
                $attributes,
                $pks,
                $existence,
                $parameters->getPath()
            );

            $parameters->getCommandQueue()->add($jsonUpdateCommand->getDefinition(), $jsonUpdateCommand);
        }
    }
}
