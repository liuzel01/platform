<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class StreamNotReadableException extends ShuweiHttpException
{
    public function __construct(string $path)
    {
        parent::__construct(
            'Could not read stream at following path: "{{ path }}"',
            ['path' => $path]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_STREAM_NOT_READABLE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
