<?php declare(strict_types=1);

namespace Shuwei\Core\System\SystemConfig\Facade;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Shuwei\Core\Framework\Script\Execution\Awareness\WebsiteContextAware;
use Shuwei\Core\Framework\Script\Execution\Hook;
use Shuwei\Core\Framework\Script\Execution\Script;
use Shuwei\Core\System\SystemConfig\SystemConfigService;

/**
 * @internal
 */
#[Package('system-settings')]
class SystemConfigFacadeHookFactory extends HookServiceFactory
{
    /**
     * @internal
     */
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly Connection $connection
    ) {
    }

    public function getName(): string
    {
        return 'config';
    }

    public function factory(Hook $hook, Script $script): SystemConfigFacade
    {
        $websiteId = null;

        if ($hook instanceof WebsiteContextAware) {
            $websiteId = $hook->getWebsiteContext()->getWebsiteId();
        }

        return new SystemConfigFacade($this->systemConfigService, $this->connection, $script->getScriptAppInformation(), $websiteId);
    }
}
