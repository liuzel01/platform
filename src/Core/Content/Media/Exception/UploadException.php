<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class UploadException extends ShuweiHttpException
{
    public function __construct(string $message = '')
    {
        parent::__construct('{{ message }}', ['message' => $message]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_UPLOAD';
    }
}
