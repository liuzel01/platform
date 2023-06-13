<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing\Facade;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Script\Execution\Awareness\HookServiceFactory;
use Shuwei\Core\Framework\Script\Execution\Hook;
use Shuwei\Core\Framework\Script\Execution\Script;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
#[Package('core')]
class RequestFacadeFactory extends HookServiceFactory
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function factory(Hook $hook, Script $script): RequestFacade
    {
        $request = $this->requestStack->getMainRequest();
        \assert($request !== null);

        return new RequestFacade($request);
    }

    public function getName(): string
    {
        return 'request';
    }
}
