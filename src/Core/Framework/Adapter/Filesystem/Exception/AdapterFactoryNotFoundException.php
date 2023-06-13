<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Filesystem\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class AdapterFactoryNotFoundException extends ShuweiHttpException
{
    public function __construct(string $type)
    {
        parent::__construct(
            'Adapter factory for type "{{ type }}" was not found.',
            ['type' => $type]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__FILESYSTEM_ADAPTER_NOT_FOUND';
    }
}
