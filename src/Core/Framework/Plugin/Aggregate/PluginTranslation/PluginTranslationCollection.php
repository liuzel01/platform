<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin\Aggregate\PluginTranslation;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<PluginTranslationEntity>
 */
#[Package('core')]
class PluginTranslationCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'plugin_translation_collection';
    }

    protected function getExpectedClass(): string
    {
        return PluginTranslationEntity::class;
    }
}
