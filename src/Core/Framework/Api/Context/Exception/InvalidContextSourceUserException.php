<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Context\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class InvalidContextSourceUserException extends ShuweiHttpException
{
    public function __construct(string $contextSource)
    {
        parent::__construct(
            '{{ contextSource }} does not have a valid user ID',
            ['contextSource' => $contextSource]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_CONTEXT_SOURCE_USER';
    }
}
