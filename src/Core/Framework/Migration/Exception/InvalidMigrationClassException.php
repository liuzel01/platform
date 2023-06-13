<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Migration\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class InvalidMigrationClassException extends ShuweiHttpException
{
    public function __construct(
        string $class,
        string $path
    ) {
        parent::__construct(
            'Unable to load migration {{ class }} at path {{ path }}',
            ['class' => $class, 'path' => $path]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_MIGRATION';
    }
}
