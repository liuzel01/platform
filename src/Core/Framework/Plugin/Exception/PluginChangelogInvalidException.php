<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class PluginChangelogInvalidException extends ShuweiHttpException
{
    public function __construct(string $changelogPath)
    {
        parent::__construct(
            'The changelog of "{{ changelogPath }}" is invalid.',
            ['changelogPath' => $changelogPath]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__PLUGIN_CHANGELOG_INVALID';
    }
}
