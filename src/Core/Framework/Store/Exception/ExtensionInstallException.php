<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('merchant-services')]
class ExtensionInstallException extends ShuweiHttpException
{
    public function getErrorCode(): string
    {
        return 'FRAMEWORK__EXTENSION_INSTALL_EXCEPTION';
    }
}
