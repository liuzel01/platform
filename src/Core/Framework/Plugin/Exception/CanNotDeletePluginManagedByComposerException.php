<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class CanNotDeletePluginManagedByComposerException extends ShuweiHttpException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            'Can not delete plugin. Please contact your system administrator. Error: {{ reason }}',
            ['reason' => $reason]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_CANNOT_DELETE_PLUGIN_MANAGED_BY_SHUWEI';
    }
}
