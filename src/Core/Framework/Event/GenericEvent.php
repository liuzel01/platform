<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event;

use Shuwei\Core\Framework\Log\Package;

#[Package('business-ops')]
interface GenericEvent
{
    public function getName(): string;
}
