<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class MediaNotFoundException extends ShuweiHttpException
{
    public function __construct(string $mediaId)
    {
        parent::__construct(
            'Media for id {{ mediaId }} not found.',
            ['mediaId' => $mediaId]
        );
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_NOT_FOUND';
    }
}
