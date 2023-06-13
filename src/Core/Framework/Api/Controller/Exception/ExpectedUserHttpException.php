<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Controller\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class ExpectedUserHttpException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('For this interaction an authenticated user login is required.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__EXPECTED_USER';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
