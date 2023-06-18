<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Event;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('system-settings')]
class SystemConfigDomainLoadedEvent extends Event
{
    public function __construct(
        private readonly string $domain,
        private array $config,
    ) {
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }
}
