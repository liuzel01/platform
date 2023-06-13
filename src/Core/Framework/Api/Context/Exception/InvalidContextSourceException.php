<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Context\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class InvalidContextSourceException extends ShuweiHttpException
{
    public function __construct(
        string $expected,
        string $actual
    ) {
        parent::__construct(
            'Expected ContextSource of "{{expected}}", but got "{{actual}}".',
            ['expected' => $expected, 'actual' => $actual]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_CONTEXT_SOURCE';
    }
}
