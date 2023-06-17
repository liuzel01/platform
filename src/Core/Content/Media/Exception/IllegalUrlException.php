<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class IllegalUrlException extends ShuweiHttpException
{
    public function __construct(string $url)
    {
        parent::__construct(
            'Provided URL "{{ url }}" is not allowed.',
            ['url' => $url]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_ILLEGAL_URL';
    }
}
