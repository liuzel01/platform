<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\Request;

#[Package('core')]
class RouteScope extends AbstractRouteScope
{
    /**
     * @var array<string>
     */
    protected $allowedPaths = ['_wdt', '_profiler', '_error'];

    public function isAllowed(Request $request): bool
    {
        return true;
    }

    public function getId(): string
    {
        return 'default';
    }
}
