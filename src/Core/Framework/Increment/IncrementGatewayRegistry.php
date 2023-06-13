<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Increment;

use Shuwei\Core\Framework\Increment\Exception\IncrementGatewayNotFoundException;
use Shuwei\Core\Framework\Log\Package;

/**
 * @final
 */
#[Package('core')]
class IncrementGatewayRegistry
{
    final public const MESSAGE_QUEUE_POOL = 'message_queue';
    final public const USER_ACTIVITY_POOL = 'user_activity';

    /**
     * @param AbstractIncrementer[] $gateways
     */
    public function __construct(private readonly iterable $gateways)
    {
    }

    public function get(string $pool): AbstractIncrementer
    {
        foreach ($this->gateways as $gateway) {
            if ($gateway->getPool() === $pool) {
                return $gateway;
            }
        }

        throw new IncrementGatewayNotFoundException($pool);
    }
}
