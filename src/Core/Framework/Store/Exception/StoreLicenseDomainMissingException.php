<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('merchant-services')]
class StoreLicenseDomainMissingException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('Store license domain is missing');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_LICENSE_DOMAIN_IS_MISSING';
    }
}
