<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Sync;

use Doctrine\DBAL\ConnectionException;
use Shuwei\Core\Framework\Api\Exception\InvalidSyncOperationException;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface SyncServiceInterface
{
    /**
     * @param SyncOperation[] $operations
     *
     * @throws ConnectionException
     * @throws InvalidSyncOperationException
     */
    public function sync(array $operations, Context $context, SyncBehavior $behavior): SyncResult;
}
