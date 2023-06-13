<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event;

use Shuwei\Core\Framework\Event\EventData\EventDataCollection;
use Shuwei\Core\Framework\Log\Package;

#[Package('business-ops')]
interface FlowEventAware extends ShuweiEvent
{
    public static function getAvailableData(): EventDataCollection;

    public function getName(): string;
}
