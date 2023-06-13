<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class MissingFieldSerializerException extends ShuweiHttpException
{
    public function __construct(Field $field)
    {
        parent::__construct('No field serializer class found for field class "{{ class }}".', ['class' => $field::class]);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__MISSING_FIELD_SERIALIZER';
    }
}
