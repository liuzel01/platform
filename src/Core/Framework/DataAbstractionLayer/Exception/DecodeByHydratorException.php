<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\DataAbstractionLayer\Field\Field;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class DecodeByHydratorException extends ShuweiHttpException
{
    public function __construct(Field $field)
    {
        parent::__construct(
            'Decoding of {{ fieldClass }} is handled by the entity hydrator.',
            ['fieldClass' => $field::class]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__DECODING_HANDLED_BY_ENTITY_HYDRATOR';
    }
}
