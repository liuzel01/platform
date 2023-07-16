<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\Adapter\Cache\AbstractCacheTracer;
use Shuwei\Core\Framework\Adapter\Cache\CacheValueCompressor;
use Shuwei\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\Json;
use Shuwei\Core\System\Country\Event\CountryRouteCacheKeyEvent;
use Shuwei\Core\System\Country\Event\CountryRouteCacheTagsEvent;
use Frontend\FrontendContext;
use Frontend\Website\StoreApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('system-settings')]
class CachedCountryRoute extends AbstractCountryRoute
{
    final public const ALL_TAG = 'country-route';

    /**
     * @internal
     *
     * @param AbstractCacheTracer<CountryRouteResponse> $tracer
     * @param array<string> $states
     */
    public function __construct(
        private readonly AbstractCountryRoute $decorated,
        private readonly CacheInterface $cache,
        private readonly EntityCacheKeyGenerator $generator,
        private readonly AbstractCacheTracer $tracer,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly array $states
    ) {
    }

    public static function buildName(string $id): string
    {
        return 'country-route-' . $id;
    }

    #[Route(path: '/store-api/country', name: 'store-api.country', methods: ['GET', 'POST'], defaults: ['_entity' => 'country'])]
    public function load(Request $request, Criteria $criteria, FrontendContext $context): CountryRouteResponse
    {
        if ($context->hasState(...$this->states)) {
            return $this->getDecorated()->load($request, $criteria, $context);
        }

        $key = $this->generateKey($request, $context, $criteria);

        if ($key === null) {
            return $this->getDecorated()->load($request, $criteria, $context);
        }

        $value = $this->cache->get($key, function (ItemInterface $item) use ($request, $context, $criteria) {
            $name = self::buildName($context->getWebsiteId());

            $response = $this->tracer->trace($name, fn () => $this->getDecorated()->load($request, $criteria, $context));

            $item->tag($this->generateTags($request, $response, $context, $criteria));

            return CacheValueCompressor::compress($response);
        });

        return CacheValueCompressor::uncompress($value);
    }

    protected function getDecorated(): AbstractCountryRoute
    {
        return $this->decorated;
    }

    private function generateKey(Request $request, FrontendContext $context, Criteria $criteria): ?string
    {
        $parts = [
            $this->generator->getCriteriaHash($criteria),
            $this->generator->getFrontendContextHash($context),
        ];

        $event = new CountryRouteCacheKeyEvent($parts, $request, $context, $criteria);
        $this->dispatcher->dispatch($event);

        if (!$event->shouldCache()) {
            return null;
        }

        return self::buildName($context->getWebsiteId()) . '-' . md5(Json::encode($event->getParts()));
    }

    /**
     * @return array<string>
     */
    private function generateTags(Request $request, StoreApiResponse $response, FrontendContext $context, Criteria $criteria): array
    {
        $tags = array_merge(
            $this->tracer->get(self::buildName($context->getWebsiteId())),
            [self::buildName($context->getWebsiteId()), self::ALL_TAG]
        );

        $event = new CountryRouteCacheTagsEvent($tags, $request, $response, $context, $criteria);
        $this->dispatcher->dispatch($event);

        return array_unique(array_filter($event->getTags()));
    }
}
