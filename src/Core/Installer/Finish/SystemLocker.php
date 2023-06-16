<?php declare(strict_types=1);

namespace Shuwei\Core\Installer\Finish;

use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class SystemLocker
{
    public function __construct(private readonly string $projectDir)
    {
    }

    public function lock(): void
    {
        file_put_contents($this->projectDir . '/install.lock', date('YmdHi'));
    }
}
