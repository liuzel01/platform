<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Acl\Event;

use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class CommandAclValidationEvent extends Event
{
    public function __construct(
        private array $missingPrivileges,
        private readonly AdminApiSource $source,
        private readonly WriteCommand $command
    ) {
    }

    public function getMissingPrivileges(): array
    {
        return $this->missingPrivileges;
    }

    public function addMissingPrivilege(string $privilege): void
    {
        $this->missingPrivileges[] = $privilege;
    }

    public function getSource(): AdminApiSource
    {
        return $this->source;
    }

    public function getCommand(): WriteCommand
    {
        return $this->command;
    }
}
