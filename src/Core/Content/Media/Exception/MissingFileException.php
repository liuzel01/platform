<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class MissingFileException extends ShuweiHttpException
{
    public function __construct(string $mediaId)
    {
        parent::__construct(
            'Could not find file for media with id: "{{ mediaId }}"',
            ['mediaId' => $mediaId]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_MISSING_FILE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
