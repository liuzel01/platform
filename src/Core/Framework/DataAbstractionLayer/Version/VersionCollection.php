<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Version;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<VersionEntity>
 */
#[Package('core')]
class VersionCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'dal_version_collection';
    }

    protected function getExpectedClass(): string
    {
        return VersionEntity::class;
    }
}
