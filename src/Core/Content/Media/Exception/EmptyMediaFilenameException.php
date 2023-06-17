<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class EmptyMediaFilenameException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct('A valid filename must be provided.');
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_EMPTY_FILE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
