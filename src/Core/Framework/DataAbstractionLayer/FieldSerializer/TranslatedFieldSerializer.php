<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Shuwei\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class TranslatedFieldSerializer implements FieldSerializerInterface
{
    public function normalize(Field $field, array $data, WriteParameterBag $parameters): array
    {
        if (!$field instanceof TranslatedField) {
            throw DataAbstractionLayerException::invalidSerializerField(TranslatedField::class, $field);
        }
        $key = $field->getPropertyName();
        if (!\array_key_exists($key, $data)) {
            return $data;
        }

        $value = $data[$key];

        $translatedField = EntityDefinitionQueryHelper::getTranslatedField($parameters->getDefinition(), $field);

        if (\is_array($value) && $translatedField instanceof JsonField === false) {
            foreach ($value as $translationKey => $translationValue) {
                if (!isset($data['translations'][$translationKey][$key])) {
                    $data['translations'][$translationKey][$key] = $translationValue;
                }
            }

            return $data;
        }

        $contextLanguage = $parameters->getContext()->getContext()->getLanguageId();
        if (!isset($data['translations'][$contextLanguage][$key])) {
            // use the default language from the context
            $data['translations'][$contextLanguage][$key] = $value;
        }

        return $data;
    }

    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        yield from [];
    }

    public function decode(Field $field, mixed $value): ?string
    {
        if ($value === null) {
            return $value;
        }

        return (string) $value;
    }
}
