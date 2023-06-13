<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('system-settings')]
class BundleConfigNotFoundException extends ShuweiHttpException
{
    public function __construct(
        string $configPath,
        string $bundleName
    ) {
        parent::__construct(
            'Could not find "{{ configPath }}" for bundle "{{ bundle }}".',
            [
                'configPath' => $configPath,
                'bundle' => $bundleName,
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'SYSTEM__BUNDLE_CONFIG_NOT_FOUND';
    }
}
