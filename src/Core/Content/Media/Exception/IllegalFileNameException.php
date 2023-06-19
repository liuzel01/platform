<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class IllegalFileNameException extends ShuweiHttpException
{
    public function __construct(
        string $filename,
        string $cause
    ) {
        parent::__construct(
            'Provided filename "{{ fileName }}" is not permitted: {{ cause }}',
            ['fileName' => $filename, 'cause' => $cause]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_ILLEGAL_FILE_NAME';
    }
}
