<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Event;

use Shuwei\Core\Framework\Adapter\Cache\StoreApiRouteCacheKeyEvent;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class CountryStateRouteCacheKeyEvent extends StoreApiRouteCacheKeyEvent
{
}
