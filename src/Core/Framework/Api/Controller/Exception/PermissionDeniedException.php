<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Controller\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class PermissionDeniedException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('The user does not have the permission to do this action.');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PERMISSION_DENIED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
