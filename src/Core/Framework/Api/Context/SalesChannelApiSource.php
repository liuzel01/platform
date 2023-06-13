<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Context;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\JsonSerializableTrait;

#[Package('core')]
class SalesChannelApiSource implements ContextSource, \JsonSerializable
{
    use JsonSerializableTrait;

    public string $type = 'sales-channel';

    public function __construct(private readonly string $salesChannelId)
    {
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }
}
