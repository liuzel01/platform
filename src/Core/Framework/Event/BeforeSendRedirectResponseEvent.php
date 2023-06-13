<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class BeforeSendRedirectResponseEvent extends BeforeSendResponseEvent
{
}
