<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Context;

use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class ShopApiSource extends SalesChannelApiSource
{
    public string $type = 'shop-api';
}
