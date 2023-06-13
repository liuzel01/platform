<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Facade;

use Shuwei\Core\Framework\Api\Sync\SyncService;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Shuwei\Core\Framework\Script\Execution\Hook;
use Shuwei\Core\Framework\Script\Execution\Script;

/**
 * @internal
 */
#[Package('core')]
class RepositoryWriterFacadeHookFactory extends HookServiceFactory
{
    public function __construct(
        private readonly DefinitionInstanceRegistry $registry,
        private readonly AppContextCreator $appContextCreator,
        private readonly SyncService $syncService
    ) {
    }

    public function factory(Hook $hook, Script $script): RepositoryWriterFacade
    {
        return new RepositoryWriterFacade(
            $this->registry,
            $this->syncService,
            $this->appContextCreator->getAppContext($hook, $script)
        );
    }

    public function getName(): string
    {
        return 'writer';
    }
}
