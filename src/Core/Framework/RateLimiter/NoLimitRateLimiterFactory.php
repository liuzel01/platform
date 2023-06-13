<?php
declare(strict_types=1);

namespace Shuwei\Core\Framework\RateLimiter;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Test\RateLimiter\DisableRateLimiterCompilerPass;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\Policy\NoLimiter;

#[Package('core')]
class NoLimitRateLimiterFactory extends RateLimiterFactory
{
    public function __construct(private readonly RateLimiterFactory $rateLimiterFactory)
    {
    }

    public function create(?string $key = null): LimiterInterface
    {
        if (DisableRateLimiterCompilerPass::isDisabled()) {
            return new NoLimiter();
        }

        return $this->rateLimiterFactory->create($key);
    }
}
