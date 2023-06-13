<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Migration\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class UnknownMigrationSourceException extends ShuweiHttpException
{
    public function __construct(private readonly string $name)
    {
        parent::__construct(
            'No source registered for "{{ name }}"',
            ['name' => $name]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_MIGRATION_SOURCE';
    }

    public function getParameters(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
