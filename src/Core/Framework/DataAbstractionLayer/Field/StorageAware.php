<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Field;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
interface StorageAware
{
    public function getStorageName(): string;
}
