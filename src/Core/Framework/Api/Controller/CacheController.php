<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Controller;

use Shuwei\Core\Framework\Adapter\Cache\CacheClearer;
use Shuwei\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\Random;
use Shuwei\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
#[Package('system-settings')]
class CacheController extends AbstractController
{
    /**
     * @internal
     */
    public function __construct(
        private readonly CacheClearer $cacheClearer,
        private readonly AdapterInterface $adapter,
        private readonly EntityIndexerRegistry $indexerRegistry
    ) {
    }

    #[Route(path: '/api/_action/cache_info', name: 'api.action.cache.info', defaults: ['_acl' => ['system:cache:info']], methods: ['GET'])]
    public function info(): JsonResponse
    {
        return new JsonResponse([
            'environment' => $this->getParameter('kernel.environment'),
            'httpCache' => $this->container->get('parameter_bag')->has('shuwei.http.cache.enabled') && $this->getParameter('shuwei.http.cache.enabled'),
            'cacheAdapter' => $this->getUsedCache($this->adapter),
        ]);
    }

    #[Route(path: '/api/_action/index', name: 'api.action.cache.index', defaults: ['_acl' => ['api_action_cache_index']], methods: ['POST'])]
    public function index(RequestDataBag $dataBag): Response
    {
        $data = $dataBag->all();
        $skip = !empty($data['skip']) && \is_array($data['skip']) ? $data['skip'] : [];

        $this->indexerRegistry->sendIndexingMessage([], $skip);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/api/_action/cache', name: 'api.action.cache.delete', defaults: ['_acl' => ['system:clear:cache']], methods: ['DELETE'])]
    public function clearCache(): Response
    {
        $this->cacheClearer->clear();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/api/_action/cleanup', name: 'api.action.cache.cleanup', defaults: ['_acl' => ['system:clear:cache']], methods: ['DELETE'])]
    public function clearOldCacheFolders(): Response
    {
        $this->cacheClearer->scheduleCacheFolderCleanup();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/api/_action/container_cache', name: 'api.action.container-cache.delete', defaults: ['_acl' => ['system:clear:cache']], methods: ['DELETE'])]
    public function clearContainerCache(): Response
    {
        $this->cacheClearer->clearContainerCache();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function getUsedCache(AdapterInterface $adapter): string
    {
        if ($adapter instanceof TagAwareAdapter || $adapter instanceof TraceableAdapter) {
            // Do not declare function as static
            $func = \Closure::bind(fn () => $adapter->getPool(), $adapter, $adapter::class);

            $adapter = $func();
        }

        if ($adapter instanceof TraceableAdapter) {
            return $this->getUsedCache($adapter);
        }

        $name = $adapter::class;
        \assert(\is_string($name));
        $parts = explode('\\', $name);
        $name = str_replace('Adapter', '', end($parts));

        return $name;
    }
}
