<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class UnsupportedEncoderInputException extends ShuweiHttpException
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Unsupported encoder data provided. Only entities and entity collections are supported');
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__UNSUPPORTED_ENCODER_INPUT';
    }
}
