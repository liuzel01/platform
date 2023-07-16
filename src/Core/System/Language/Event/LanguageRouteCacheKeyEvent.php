<?php declare(strict_types=1);

namespace Shuwei\Core\System\Language\Event;

use Frontend\Framework\Adapter\Cache\FrontendApiRouteCacheKeyEvent;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class LanguageRouteCacheKeyEvent extends FrontendApiRouteCacheKeyEvent
{
}
