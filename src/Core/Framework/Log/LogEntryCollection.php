<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Log;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<LogEntryEntity>
 */
#[Package('core')]
class LogEntryCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'dal_log_entry_collection';
    }

    protected function getExpectedClass(): string
    {
        return LogEntryEntity::class;
    }
}
