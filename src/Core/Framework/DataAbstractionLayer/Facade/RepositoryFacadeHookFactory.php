<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Facade;

use Shuwei\Core\Framework\Api\Acl\AclCriteriaValidator;
use Shuwei\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\RequestCriteriaBuilder;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Shuwei\Core\Framework\Script\Execution\Hook;
use Shuwei\Core\Framework\Script\Execution\Script;

/**
 * @internal
 */
#[Package('core')]
class RepositoryFacadeHookFactory extends HookServiceFactory
{
    /**
     * @internal
     */
    public function __construct(
        private readonly DefinitionInstanceRegistry $registry,
        private readonly AppContextCreator $appContextCreator,
        private readonly RequestCriteriaBuilder $criteriaBuilder,
        private readonly AclCriteriaValidator $criteriaValidator
    ) {
    }

    public function factory(Hook $hook, Script $script): RepositoryFacade
    {
        return new RepositoryFacade(
            $this->registry,
            $this->criteriaBuilder,
            $this->criteriaValidator,
            $this->appContextCreator->getAppContext($hook, $script)
        );
    }

    public function getName(): string
    {
        return 'repository';
    }
}
