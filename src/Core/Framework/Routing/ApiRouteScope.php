<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Api\Context\SystemSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\Request;

#[Package('core')]
class ApiRouteScope extends AbstractRouteScope implements ApiContextRouteScopeDependant
{
    final public const ID = 'api';

    /**
     * @var array<string>
     */
    protected $allowedPaths = ['api', 'sw-domain-hash.html'];

    public function isAllowed(Request $request): bool
    {
        /** @var Context $context */
        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT);
        $authRequired = $request->attributes->get('auth_required', true);
        $source = $context->getSource();

        if (!$authRequired) {
            return $source instanceof SystemSource || $source instanceof AdminApiSource;
        }

        return $context->getSource() instanceof AdminApiSource;
    }

    public function getId(): string
    {
        return self::ID;
    }
}
