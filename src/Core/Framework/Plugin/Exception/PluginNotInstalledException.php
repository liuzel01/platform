<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class PluginNotInstalledException extends ShuweiHttpException
{
    public function __construct(string $pluginName)
    {
        parent::__construct(
            'Plugin "{{ plugin }}" is not installed.',
            ['plugin' => $pluginName]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_NOT_INSTALLED';
    }
}
