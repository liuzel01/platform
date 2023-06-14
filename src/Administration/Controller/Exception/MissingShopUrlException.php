<?php declare(strict_types=1);

namespace Shuwei\Administration\Controller\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('administration')]
class MissingShopUrlException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('Failed to retrieve the shop url.');
    }

    public function getErrorCode(): string
    {
        return 'ADMINISTRATION__MISSING_SHOP_URL';
    }
}
