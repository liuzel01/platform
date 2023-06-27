<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Facade;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Api\Exception\MissingPrivilegeException;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Script\Execution\ScriptAppInformation;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\System\SystemConfig\SystemConfigService;

/**
 * The `config` service allows you to access the shop's and your app's configuration values.
 *
 * @script-service miscellaneous
 */
#[Package('system-settings')]
class SystemConfigFacade
{
    private const PRIVILEGE = 'system_config:read';

    private array $appData = [];

    /**
     * @internal
     */
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly Connection $connection,
        private readonly ?ScriptAppInformation $scriptAppInformation,
        private readonly ?string $salesChannelId
    ) {
    }

    /**
     * The `get()` method allows you to access all config values of the store.
     * Notice that your app needs the `system_config:read` privilege to use this method.
     *
     * @param string $key The key of the configuration value e.g. `core.listing.defaultSorting`.
     * @param string|null $salesChannelId The SalesChannelId if you need the config value for a specific Website, if you don't provide a SalesChannelId, the one of the current Context is used as default.
     *
     * @return array|bool|float|int|string|null
     *
     * @example test-config/script.twig 4 1 Read an arbitrary system_config value.
     */
    public function get(string $key)
    {
        return $this->systemConfigService->get($key);
    }

    /**
     * The `app()` method allows you to access the config values your app's configuration.
     * Notice that your app does not need any additional privileges to use this method, as you can only access your own app's configuration.
     *
     * @param string $key The name of the configuration value specified in the config.xml e.g. `exampleTextField`.
     * @param string|null $salesChannelId The SalesChannelId if you need the config value for a specific Website, if you don't provide a SalesChannelId, the one of the current Context is used as default.
     *
     * @return array|bool|float|int|string|null
     *
     * @example test-config/script.twig 5 1 Read your app's config value.
     */
    public function app(string $key, ?string $salesChannelId = null)
    {
        if (!$this->scriptAppInformation) {
            throw new \BadMethodCallException('`config.app()` can only be called from app scripts.');
        }

        if (!$salesChannelId) {
            $salesChannelId = $this->salesChannelId;
        }

        $key = $this->scriptAppInformation->getAppName() . '.config.' . $key;

        return $this->systemConfigService->get($key, $salesChannelId);
    }
}
