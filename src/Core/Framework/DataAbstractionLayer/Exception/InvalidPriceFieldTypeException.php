<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class InvalidPriceFieldTypeException extends ShuweiHttpException
{
    public function __construct(string $type)
    {
        parent::__construct(
            'The price field does not contain a valid "type" value. Received {{ type }} ',
            ['type' => $type]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_PRICE_FIELD_TYPE';
    }
}
