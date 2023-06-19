<?php declare(strict_types=1);

namespace Shuwei\Core\Profiling\Routing;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Routing\RouteScopeWhitelistInterface;
use Shuwei\Core\Profiling\Controller\ProfilerController;

#[Package('core')]
class ProfilerWhitelist implements RouteScopeWhitelistInterface
{
    public function applies(string $controllerClass): bool
    {
        return $controllerClass === ProfilerController::class;
    }
}
