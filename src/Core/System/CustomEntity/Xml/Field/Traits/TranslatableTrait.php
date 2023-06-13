<?php declare(strict_types=1);

namespace Shuwei\Core\System\CustomEntity\Xml\Field\Traits;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
trait TranslatableTrait
{
    protected bool $translatable;

    public function isTranslatable(): bool
    {
        return $this->translatable;
    }
}
