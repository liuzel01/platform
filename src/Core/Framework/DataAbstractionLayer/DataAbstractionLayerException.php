<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Framework\DataAbstractionLayer\Exception\InvalidSerializerFieldException;
use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\HttpException;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class DataAbstractionLayerException extends HttpException
{
    public const INVALID_FIELD_SERIALIZER_CODE = 'FRAMEWORK__INVALID_FIELD_SERIALIZER';

    public static function invalidSerializerField(string $expectedClass, Field $field): self
    {
        if (!Feature::isActive('v6.6.0.0')) {
            new InvalidSerializerFieldException($expectedClass, $field);
        }

        return new self(
            Response::HTTP_BAD_REQUEST,
            self::INVALID_FIELD_SERIALIZER_CODE,
            'Expected field of type "{{ expectedField }}" got "{{ field }}".',
            ['expectedField' => $expectedClass, 'field' => $field::class]
        );
    }
}
