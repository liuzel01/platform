<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Kernel;

/**
 * @internal
 */
#[Package('merchant-services')]
class InstanceService
{
    public function __construct(
        private readonly string $shuweiVersion,
        private readonly ?string $instanceId
    ) {
    }

    public function getShuweiVersion(): string
    {
        if ($this->shuweiVersion === Kernel::SHUWEI_FALLBACK_VERSION) {
            return '___VERSION___';
        }

        return $this->shuweiVersion;
    }

    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }
}
