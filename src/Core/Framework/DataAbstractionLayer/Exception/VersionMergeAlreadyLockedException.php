<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Exception;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;

#[Package('core')]
class VersionMergeAlreadyLockedException extends ShuweiHttpException
{
    public function __construct(string $versionId)
    {
        parent::__construct(
            'Merging of version {{ versionId }} is locked, as the merge is already running by another process.',
            ['versionId' => $versionId]
        );
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__VERSION_MERGE_ALREADY_LOCKED';
    }
}
