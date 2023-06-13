<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Filesystem\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class DuplicateFilesystemFactoryException extends ShuweiHttpException
{
    public function __construct(string $type)
    {
        parent::__construct('The type of factory "{{ type }}" must be unique.', ['type' => $type]);
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__DUPLICATE_FILESYSTEM_FACTORY';
    }
}
