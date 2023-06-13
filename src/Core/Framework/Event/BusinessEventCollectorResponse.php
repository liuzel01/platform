<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\Collection;

/**
 * @extends Collection<BusinessEventDefinition>
 */
#[Package('business-ops')]
class BusinessEventCollectorResponse extends Collection
{
    protected function getExpectedClass(): ?string
    {
        return BusinessEventDefinition::class;
    }
}
