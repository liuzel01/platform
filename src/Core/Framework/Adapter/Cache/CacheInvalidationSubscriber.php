<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Adapter\Cache;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal - The functions inside this class are no public-api and can be changed without previous deprecation
 */
#[Package('core')]
class CacheInvalidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CacheInvalidator $cacheInvalidator,
        private readonly Connection $connection
    ) {
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [

        ];
    }

   
}
