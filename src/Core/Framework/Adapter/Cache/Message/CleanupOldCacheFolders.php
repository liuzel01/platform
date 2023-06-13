<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Cache\Message;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\AsyncMessageInterface;

#[Package('core')]
class CleanupOldCacheFolders implements AsyncMessageInterface
{
}
