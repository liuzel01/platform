<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class CouldNotRenameFileException extends ShuweiHttpException
{
    public function __construct(
        string $mediaId,
        string $oldFileName
    ) {
        parent::__construct(
            'Could not rename file for media with id: {{ mediaId }}. Rollback to filename: "{{ oldFileName }}"',
            ['mediaId' => $mediaId, 'oldFileName' => $oldFileName]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_COULD_NOT_RENAME_FILE';
    }
}
