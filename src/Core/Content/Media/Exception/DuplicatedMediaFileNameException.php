<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class DuplicatedMediaFileNameException extends ShuweiHttpException
{
    public function __construct(
        string $fileName,
        string $fileExtension
    ) {
        parent::__construct(
            'A file with the name "{{ fileName }}.{{ fileExtension }}" already exists.',
            ['fileName' => $fileName, 'fileExtension' => $fileExtension]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_DUPLICATED_FILE_NAME';
    }
}
