<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Cache;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Package('core')]
class InvalidateCacheTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'shuwei.invalidate_cache';
    }

    public static function getDefaultInterval(): int
    {
        return 20;
    }

    public static function shouldRun(ParameterBagInterface $bag): bool
    {
        return $bag->get('shuwei.cache.invalidation.delay') > 0;
    }
}
