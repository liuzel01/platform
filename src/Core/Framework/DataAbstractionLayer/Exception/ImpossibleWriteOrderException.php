<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class ImpossibleWriteOrderException extends ShuweiHttpException
{
    public function __construct(array $remaining)
    {
        parent::__construct(
            'Can not resolve write order for provided data. Remaining write order classes: {{ classesString }}',
            ['classes' => $remaining, 'classesString' => implode(', ', $remaining)]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__IMPOSSIBLE_WRITE_ORDER';
    }
}
