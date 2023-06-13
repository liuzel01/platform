<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\FieldSerializer;

use Shuwei\Core\Framework\DataAbstractionLayer\DataAbstractionLayerException;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\AllowEmptyString;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\DataStack\KeyValuePair;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteParameterBag;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\HtmlSanitizer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
#[Package('core')]
class StringFieldSerializer extends AbstractFieldSerializer
{
    /**
     * @internal
     */
    public function __construct(
        ValidatorInterface $validator,
        DefinitionInstanceRegistry $definitionRegistry,
        private readonly HtmlSanitizer $sanitizer
    ) {
        parent::__construct($validator, $definitionRegistry);
    }

    public function encode(
        Field $field,
        EntityExistence $existence,
        KeyValuePair $data,
        WriteParameterBag $parameters
    ): \Generator {
        if (!$field instanceof StringField) {
            throw DataAbstractionLayerException::invalidSerializerField(StringField::class, $field);
        }

        $tmp = $data->getValue();
        if (\is_string($tmp)) {
            $tmp = trim($tmp);
        }

        if ($tmp === '' && !$field->is(AllowEmptyString::class)) {
            $data->setValue(null);
        }

        $this->validateIfNeeded($field, $existence, $data, $parameters);

        $data->setValue($this->sanitize($this->sanitizer, $data, $field, $existence));

        $this->validateIfNeeded($field, $existence, $data, $parameters);

        yield $field->getStorageName() => $data->getValue() !== null ? (string) $data->getValue() : null;
    }

    public function decode(Field $field, mixed $value): ?string
    {
        if ($value === null) {
            return $value;
        }

        return (string) $value;
    }

    /**
     * @param StringField $field
     *
     * @return Constraint[]
     */
    protected function getConstraints(Field $field): array
    {
        $constraints = [
            new Type('string'),
            new Length(['max' => $field->getMaxLength()]),
        ];

        if (!$field->is(AllowEmptyString::class)) {
            $constraints[] = new NotBlank();
        }

        if ($field->is(AllowEmptyString::class) && $field->is(Required::class)) {
            $constraints[] = new NotNull();
        }

        return $constraints;
    }
}
