<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\Command;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\EntityExistence;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class JsonUpdateCommand extends UpdateCommand
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string> $primaryKey
     */
    public function __construct(
        EntityDefinition $definition,
        private readonly string $storageName,
        array $payload,
        array $primaryKey,
        EntityExistence $existence,
        string $path
    ) {
        parent::__construct($definition, $payload, $primaryKey, $existence, $path);
    }

    public function getStorageName(): string
    {
        return $this->storageName;
    }
}
