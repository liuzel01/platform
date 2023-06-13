<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\MessageQueue\ScheduledTask;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\AsyncMessageInterface;

#[Package('core')]
class RegisterScheduledTaskMessage implements AsyncMessageInterface
{
}
