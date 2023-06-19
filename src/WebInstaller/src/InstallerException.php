<?php declare(strict_types=1);

namespace Shuwei\WebInstaller;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class InstallerException extends \RuntimeException
{
    public static function cannotFindShuweiInstallation(): self
    {
        return new self('Could not find Shuwei installation');
    }

    public static function cannotFindComposerLock(): self
    {
        return new self('Could not find composer.lock file');
    }

    public static function cannotFindShuweiInComposerLock(): self
    {
        return new self('Could not find Shuwei in composer.lock file');
    }
}
