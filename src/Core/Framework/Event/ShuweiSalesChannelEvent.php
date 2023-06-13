<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Event;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\SalesChannel\SalesChannelContext;

#[Package('core')]
interface ShuweiSalesChannelEvent extends ShuweiEvent
{
    public function getSalesChannelContext(): SalesChannelContext;
}
