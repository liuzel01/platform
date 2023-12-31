<?php declare(strict_types=1);

namespace Shuwei\Core\System\Country\Website;

use Shuwei\Core\Framework\Adapter\Cache\AbstractCacheTracer;
use Shuwei\Core\Framework\Adapter\Cache\CacheValueCompressor;
use Shuwei\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\Json;
use Shuwei\Core\System\Country\Event\CountryStateRouteCacheKeyEvent;
use Shuwei\Core\System\Country\Event\CountryStateRouteCacheTagsEvent;
use Frontend\FrontendContext;
use Frontend\Website\StoreApiResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('system-settings')]
class CachedCountryStateRoute extends AbstractCountryStateRoute
{
    final public const ALL_TAG = 'country-state-route';

    /**
     * @internal
     *
     * @param AbstractCacheTracer<CountryStateRouteResponse> $tracer
     * @param array<string> $states
     */
    public function __construct(
        private readonly AbstractCountryStateRoute $decorated,
        private readonly CacheInterface $cache,
        private readonly EntityCacheKeyGenerator $generator,
        private readonly AbstractCacheTracer $tracer,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly array $states
    ) {
    }

    public static function buildName(string $id): string
    {
        return 'country-state-route-' . $id;
    }

    #[Route(path: '/store-api/country-state/{countryId}', name: 'store-api.country.state', methods: ['GET', 'POST'], defaults: ['_entity' => 'country'])]
    public function load(string $countryId, Request $request, Criteria $criteria, FrontendContext $context): CountryStateRouteResponse
    {
        if ($context->hasState(...$this->states)) {
            return $this->getDecorated()->load($countryId, $request, $criteria, $context);
        }

        $key = $this->generateKey($countryId, $request, $context, $criteria);

        if ($key === null) {
            return $this->getDecorated()->load($countryId, $request, $criteria, $context);
        }

        $value = $this->cache->get($key, function (ItemInterface $item) use ($countryId, $request, $criteria, $context) {
            $name = self::buildName($countryId);
            $response = $this->tracer->trace($name, fn () => $this->getDecorated()->load($countryId, $request, $criteria, $context));

            $item->tag($this->generateTags($countryId, $request, $response, $context, $criteria));

            return CacheValueCompressor::compress($response);
        });

        return CacheValueCompressor::uncompress($value);
    }

    protected function getDecorated(): AbstractCountryStateRoute
    {
        return $this->decorated;
    }

    private function generateKey(string $countryId, Request $request, FrontendContext $context, Criteria $criteria): ?string
    {
        $parts = [
            $countryId,
            $this->generator->getCriteriaHash($criteria),
            $context->getLanguageId(),
        ];

        $event = new CountryStateRouteCacheKeyEvent($parts, $request, $context, $criteria);
        $this->dispatcher->dispatch($event);

        if (!$event->shouldCache()) {
            return null;
        }

        return self::buildName($countryId) . '-' . md5(Json::encode($event->getParts()));
    }

    /**
     * @return array<string>
     */
    private function generateTags(string $countryId, Request $request, StoreApiResponse $response, FrontendContext $context, Criteria $criteria): array
    {
        $tags = array_merge(
            $this->tracer->get(self::buildName($countryId)),
            [self::buildName($countryId), self::ALL_TAG]
        );

        $event = new CountryStateRouteCacheTagsEvent($tags, $request, $response, $context, $criteria);
        $this->dispatcher->dispatch($event);

        return array_unique(array_filter($event->getTags()));
    }
}
