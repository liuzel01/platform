<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Migration\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class MigrateException extends ShuweiHttpException
{
    public function __construct(
        string $message,
        \Exception $previous
    ) {
        parent::__construct('Migration error: {{ errorMessage }}', ['errorMessage' => $message], $previous);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__MIGRATION_ERROR';
    }
}
