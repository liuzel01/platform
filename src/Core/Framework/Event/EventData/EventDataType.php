<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event\EventData;

use Shuwei\Core\Framework\Log\Package;

#[Package('business-ops')]
interface EventDataType
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
