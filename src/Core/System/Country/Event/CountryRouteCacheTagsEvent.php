<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Event;

use Shuwei\Core\Framework\Adapter\Cache\StoreApiRouteCacheTagsEvent;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class CountryRouteCacheTagsEvent extends StoreApiRouteCacheTagsEvent
{
}
