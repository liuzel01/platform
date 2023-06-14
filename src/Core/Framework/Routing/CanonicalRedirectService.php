<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Routing;

use Shuwei\Core\Framework\Log\Package;

use Shuwei\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class CanonicalRedirectService
{
    /**
     * @internal
     */
    public function __construct(private readonly SystemConfigService $configService)
    {
    }

    /**
     * getRedirect takes a request processed by the RequestTransformer and checks,
     * whether it points to a SEO-URL which has been superseded. In case the corresponding
     * configuration option is active, it returns a redirect response to indicate, that
     * the request should be redirected to the canonical URL.
     */
    public function getRedirect(Request $request): ?Response
    {

        $shouldRedirect = $this->configService->get('core.seo.redirectToCanonicalUrl');

        if (!$shouldRedirect) {
            return null;
        }

        if ( empty($canonical)) {
            return null;
        }

        $queryString = $request->getQueryString();

        if ($queryString) {
            $canonical = sprintf('%s?%s', $canonical, $queryString);
        }

        return new RedirectResponse($canonical, Response::HTTP_MOVED_PERMANENTLY);
    }
}
