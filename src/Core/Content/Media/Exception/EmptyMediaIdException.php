<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class EmptyMediaIdException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('A media id must be provided.');
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_EMPTY_ID';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
