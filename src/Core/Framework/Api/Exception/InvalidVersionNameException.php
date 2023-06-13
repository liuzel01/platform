<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class InvalidVersionNameException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('Invalid version name given. Only alphanumeric characters are allowed');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__INVALID_VERSION_NAME';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
