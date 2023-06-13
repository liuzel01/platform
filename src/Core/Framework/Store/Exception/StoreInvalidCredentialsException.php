<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('merchant-services')]
class StoreInvalidCredentialsException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('Invalid credentials');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_INVALID_CREDENTIALS';
    }
}
