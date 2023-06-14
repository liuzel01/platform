<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Entity;

use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class DefinitionRegistryChain
{
    public function __construct(
        private readonly DefinitionInstanceRegistry $core,
    ) {
    }

    public function get(string $class): EntityDefinition
    {
        return $this->core->get($class);
    }
}
