<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class PluginNotAZipFileException extends ShuweiHttpException
{
    public function __construct(string $mimeType)
    {
        parent::__construct(
            'Given file must be a zip file. Given: {{ mimeType }}',
            ['mimeType' => $mimeType]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_NOT_A_ZIP_FILE';
    }
}
