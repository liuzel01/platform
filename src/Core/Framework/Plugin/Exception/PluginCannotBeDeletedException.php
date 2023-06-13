<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class PluginCannotBeDeletedException extends ShuweiHttpException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            'Cannot delete plugin. Error: {{ error }}',
            ['error' => $reason]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_CANNOT_BE_DELETED';
    }
}
