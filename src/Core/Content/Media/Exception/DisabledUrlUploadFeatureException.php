<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('content')]
class DisabledUrlUploadFeatureException extends ShuweiHttpException
{
    public function __construct()
    {
        parent::__construct(
            'The feature to upload a media via URL is disabled.'
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MEDIA_URL_UPLOAD_DISABLED';
    }
}
