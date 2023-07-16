<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Event;

use Frontend\Framework\Adapter\Cache\FrontendApiRouteCacheKeyEvent;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class CountryStateRouteCacheKeyEvent extends FrontendApiRouteCacheKeyEvent
{
}
