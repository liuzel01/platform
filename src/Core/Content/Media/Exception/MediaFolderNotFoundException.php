<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('content')]
class MediaFolderNotFoundException extends ShuweiHttpException
{
    public function __construct(string $folderId)
    {
        parent::__construct(
            'Could not find media folder with id: "{{ folderId }}"',
            ['folderId' => $folderId]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_FOLDER_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
