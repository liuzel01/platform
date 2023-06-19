<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class ThumbnailCouldNotBeSavedException extends ShuweiHttpException
{
    public function __construct(string $url)
    {
        parent::__construct(
            'Thumbnail could not be saved to location: {{ location }}',
            ['location' => $url]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_THUMBNAIL_NOT_SAVED';
    }
}
