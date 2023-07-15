<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Shuwei\Core\Framework\Api\ApiDefinition\DefinitionService;
use Shuwei\Core\Framework\Api\Context\WebsiteApiSource;
use Shuwei\Core\Framework\Api\Context\SystemSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;

#[Package('core')]
class StoreApiRouteScope extends AbstractRouteScope
{
    final public const ID = DefinitionService::STORE_API;

    /**
     * @var array<string>
     */
    protected $allowedPaths = [DefinitionService::STORE_API];

    public function isAllowed(Request $request): bool
    {
        if (!$request->attributes->get('auth_required', false)) {
            return true;
        }

        /** @var Context $requestContext */
        $requestContext = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);

        if (!$request->attributes->get('auth_required', true)) {
            return $requestContext->getSource() instanceof SystemSource;
        }

        return $requestContext->getSource() instanceof WebsiteApiSource;
    }

    public function getId(): string
    {
        return static::ID;
    }
}
