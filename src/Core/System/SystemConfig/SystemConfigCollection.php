<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<SystemConfigEntity>
 */
#[Package('core')]
class SystemConfigCollection extends EntityCollection
{
    public function fieldNameInCollection(string $fieldName): bool
    {
        foreach ($this->getIterator() as $systemConfigEntity) {
            if ($systemConfigEntity->getConfigurationKey() === $fieldName) {
                return true;
            }
        }

        return false;
    }

    public function getApiAlias(): string
    {
        return 'system_config_collection';
    }

    protected function getExpectedClass(): string
    {
        return SystemConfigEntity::class;
    }
}
