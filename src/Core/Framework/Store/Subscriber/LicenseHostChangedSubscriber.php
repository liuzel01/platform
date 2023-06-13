<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Subscriber;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Store\Authentication\StoreRequestOptionsProvider;
use Shuwei\Core\System\SystemConfig\Event\SystemConfigChangedEvent;
use Shuwei\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
#[Package('merchant-services')]
class LicenseHostChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly Connection $connection
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SystemConfigChangedEvent::class => 'onLicenseHostChanged',
        ];
    }

    public function onLicenseHostChanged(SystemConfigChangedEvent $event): void
    {
        if ($event->getKey() !== StoreRequestOptionsProvider::CONFIG_KEY_STORE_LICENSE_DOMAIN) {
            return;
        }

        // The shop secret is unique for each license host and thus cannot remain the same
        $this->systemConfigService->delete(StoreRequestOptionsProvider::CONFIG_KEY_STORE_SHOP_SECRET);

        // Log out all users to enforce re-authentication
        $this->connection->executeStatement('UPDATE user SET store_token = NULL');
    }
}
