<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class MissingReverseAssociation extends ShuweiHttpException
{
    public function __construct(
        string $source,
        string $target
    ) {
        parent::__construct(
            'Can not find reverse association in entity {{ source }} which should have an association to entity {{ target }}',
            ['source' => $source, 'target' => $target]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__MISSING_REVERSE_ASSOCIATION';
    }
}
