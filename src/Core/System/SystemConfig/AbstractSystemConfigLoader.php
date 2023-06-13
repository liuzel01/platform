<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig;

use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
abstract class AbstractSystemConfigLoader
{
    abstract public function getDecorated(): AbstractSystemConfigLoader;

    abstract public function load(?string $salesChannelId): array;
}
