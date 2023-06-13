<?php declare(strict_types=1);

namespace Shuwei\Core\System\Language\Event;

use Shuwei\Core\Framework\Adapter\Cache\StoreApiRouteCacheKeyEvent;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class LanguageRouteCacheKeyEvent extends StoreApiRouteCacheKeyEvent
{
}
