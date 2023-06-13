<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<PluginEntity>
 */
#[Package('core')]
class PluginCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'plugin_collection';
    }

    protected function getExpectedClass(): string
    {
        return PluginEntity::class;
    }
}
