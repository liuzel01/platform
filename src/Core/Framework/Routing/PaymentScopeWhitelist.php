<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Shuwei\Core\Checkout\Payment\Controller\PaymentController;
use Shuwei\Core\Framework\Log\Package;

#[Package('core')]
class PaymentScopeWhitelist implements RouteScopeWhitelistInterface
{
    public function applies(string $controllerClass): bool
    {
        return $controllerClass === PaymentController::class;
    }
}
