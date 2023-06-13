<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing\Event;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Event\ShuweiSalesChannelEvent;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('core')]
class SalesChannelContextResolvedEvent extends Event implements ShuweiSalesChannelEvent
{
    public function __construct(
        private readonly SalesChannelContext $salesChannelContext,
        private readonly string $usedToken
    ) {
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getUsedToken(): string
    {
        return $this->usedToken;
    }
}
