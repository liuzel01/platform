<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('merchant-services')]
class StoreTokenMissingException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('Store token is missing');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__STORE_TOKEN_IS_MISSING';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
