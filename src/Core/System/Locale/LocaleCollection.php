<?php declare(strict_types=1);

namespace Shuwei\Core\System\Locale;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shuwei\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<LocaleEntity>
 */
#[Package('core')]
class LocaleCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'locale_collection';
    }

    protected function getExpectedClass(): string
    {
        return LocaleEntity::class;
    }
}
