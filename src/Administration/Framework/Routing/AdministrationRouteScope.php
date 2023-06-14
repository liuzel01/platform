<?php declare(strict_types=1);

namespace Shuwei\Administration\Framework\Routing;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Routing\AbstractRouteScope;
use Shuwei\Core\Framework\Routing\ApiContextRouteScopeDependant;
use Symfony\Component\HttpFoundation\Request;

#[Package('administration')]
class AdministrationRouteScope extends AbstractRouteScope implements ApiContextRouteScopeDependant
{
    final public const ID = 'administration';

    /**
     * @var string[]
     */
    protected $allowedPaths;

    /**
     * @internal
     */
    public function __construct(string $administrationPathName = 'admin')
    {
        $this->allowedPaths = [$administrationPathName, 'api'];
    }

    public function isAllowed(Request $request): bool
    {
        return true;
    }

    public function getId(): string
    {
        return self::ID;
    }
}
