<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Api\Controller;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Api\ApiDefinition\DefinitionService;
use Shuwei\Core\Framework\Api\ApiDefinition\Generator\EntitySchemaGenerator;
use Shuwei\Core\Framework\Api\ApiDefinition\Generator\OpenApi3Generator;
use Shuwei\Core\Framework\Bundle;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Event\BusinessEventCollector;
use Shuwei\Core\Framework\Increment\Exception\IncrementGatewayNotFoundException;
use Shuwei\Core\Framework\Increment\IncrementGatewayRegistry;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin;
use Shuwei\Core\Framework\Routing\RoutingException;
use Shuwei\Core\Kernel;
use Shuwei\Core\Maintenance\System\Service\AppUrlVerifier;
use Shuwei\Core\PlatformRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
#[Package('core')]
class InfoController extends AbstractController
{
    /**
     * @param array{administration?: string} $cspTemplates
     *
     * @internal
     */
    public function __construct(
        private readonly DefinitionService $definitionService,
        private readonly ParameterBagInterface $params,
        private readonly Kernel $kernel,
        private readonly Packages $packages,
        private readonly BusinessEventCollector $eventCollector,
        private readonly IncrementGatewayRegistry $incrementGatewayRegistry,
        private readonly Connection $connection,
        private readonly AppUrlVerifier $appUrlVerifier,
        private readonly array $cspTemplates = []
    ) {
    }

    #[Route(path: '/api/_info/openapi3.json', name: 'api.info.openapi3', defaults: ['auth_required' => '%shuwei.api.api_browser.auth_required_str%'], methods: ['GET'])]
    public function info(Request $request): JsonResponse
    {
        $apiType = $request->query->getAlpha('type', DefinitionService::TYPE_JSON_API);

        $apiType = $this->definitionService->toApiType($apiType);
        if ($apiType === null) {
            throw throw RoutingException::invalidRequestParameter('type');
        }

        $data = $this->definitionService->generate(OpenApi3Generator::FORMAT, DefinitionService::API, $apiType);

        return new JsonResponse($data);
    }

    #[Route(path: '/api/_info/queue.json', name: 'api.info.queue', methods: ['GET'])]
    public function queue(): JsonResponse
    {
        try {
            $gateway = $this->incrementGatewayRegistry->get(IncrementGatewayRegistry::MESSAGE_QUEUE_POOL);
        } catch (IncrementGatewayNotFoundException) {
            // In case message_queue pool is disabled
            return new JsonResponse([]);
        }

        // Fetch unlimited message_queue_stats
        $entries = $gateway->list('message_queue_stats', -1);

        return new JsonResponse(array_map(fn (array $entry) => [
            'name' => $entry['key'],
            'size' => (int) $entry['count'],
        ], array_values($entries)));
    }

    #[Route(path: '/api/_info/open-api-schema.json', name: 'api.info.open-api-schema', defaults: ['auth_required' => '%shuwei.api.api_browser.auth_required_str%'], methods: ['GET'])]
    public function openApiSchema(): JsonResponse
    {
        $data = $this->definitionService->getSchema(OpenApi3Generator::FORMAT, DefinitionService::API);

        return new JsonResponse($data);
    }

    #[Route(path: '/api/_info/entity-schema.json', name: 'api.info.entity-schema', methods: ['GET'])]
    public function entitySchema(): JsonResponse
    {
        $data = $this->definitionService->getSchema(EntitySchemaGenerator::FORMAT, DefinitionService::API);

        return new JsonResponse($data);
    }

    #[Route(path: '/api/_info/events.json', name: 'api.info.business-events', methods: ['GET'])]
    public function businessEvents(Context $context): JsonResponse
    {
        $events = $this->eventCollector->collect($context);

        return new JsonResponse($events);
    }

    #[Route(path: '/api/_info/swagger.html', name: 'api.info.swagger', defaults: ['auth_required' => '%shuwei.api.api_browser.auth_required_str%'], methods: ['GET'])]
    public function infoHtml(Request $request): Response
    {
        $nonce = $request->attributes->get(PlatformRequest::ATTRIBUTE_CSP_NONCE);
        $apiType = $request->query->getAlpha('type', DefinitionService::TYPE_JSON);
        $response = $this->render(
            '@Framework/swagger.html.twig',
            [
                'schemaUrl' => 'api.info.openapi3',
                'cspNonce' => $nonce,
                'apiType' => $apiType,
            ]
        );

        $cspTemplate = $this->cspTemplates['administration'] ?? '';
        $cspTemplate = trim($cspTemplate);
        if ($cspTemplate !== '') {
            $csp = str_replace('%nonce%', $nonce, $cspTemplate);
            $csp = str_replace(["\n", "\r"], ' ', $csp);
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }

    #[Route(path: '/api/_info/config', name: 'api.info.config', methods: ['GET'])]
    public function config(Context $context, Request $request): JsonResponse
    {
        return new JsonResponse([
            'version' => $this->params->get('kernel.shuwei_version'),
            'versionRevision' => $this->params->get('kernel.shuwei_version_revision'),
            'adminWorker' => [
                'enableAdminWorker' => $this->params->get('shuwei.admin_worker.enable_admin_worker'),
                'enableQueueStatsWorker' => $this->params->get('shuwei.admin_worker.enable_queue_stats_worker'),
                'enableNotificationWorker' => $this->params->get('shuwei.admin_worker.enable_notification_worker'),
                'transports' => $this->params->get('shuwei.admin_worker.transports'),
            ],
            'bundles' => $this->getBundles($context),
            'settings' => [
                'appUrlReachable' => $this->appUrlVerifier->isAppUrlReachable($request),
                'private_allowed_extensions' => $this->params->get('shuwei.filesystem.private_allowed_extensions'),
            ],
        ]);
    }

    #[Route(path: '/api/_info/version', name: 'api.info.shuwei.version', methods: ['GET'])]
    #[Route(path: '/api/v1/_info/version', name: 'api.info.shuwei.version_old_version', methods: ['GET'])]
    public function infoShuweiVersion(): JsonResponse
    {
        return new JsonResponse([
            'version' => $this->params->get('kernel.shuwei_version'),
        ]);
    }

    #[Route(path: '/api/_info/flow-actions.json', name: 'api.info.actions', methods: ['GET'])]
    public function flowActions(Context $context): JsonResponse
    {
        if (!$this->flowActionCollector) {
            return $this->json([]);
        }

        $events = $this->flowActionCollector->collect($context);

        return new JsonResponse($events);
    }

    /**
     * @return array<string, array{type: 'plugin', css: string[], js: string[], baseUrl: ?string }|array{type: 'app', name: string, active: bool, integrationId: string, baseUrl: string, version: string, permissions: array<string, string[]>}>
     */
    private function getBundles(Context $context): array
    {
        $assets = [];
        $package = $this->packages->getPackage('asset');

        foreach ($this->kernel->getBundles() as $bundle) {
            if (!$bundle instanceof Bundle) {
                continue;
            }

            $bundleDirectoryName = preg_replace('/bundle$/', '', mb_strtolower($bundle->getName()));
            if ($bundleDirectoryName === null) {
                throw new \RuntimeException(sprintf('Unable to generate bundle directory for bundle "%s"', $bundle->getName()));
            }

            $styles = array_map(static function (string $filename) use ($package, $bundleDirectoryName) {
                $url = 'bundles/' . $bundleDirectoryName . '/' . $filename;

                return $package->getUrl($url);
            }, $this->getAdministrationStyles($bundle));

            $scripts = array_map(static function (string $filename) use ($package, $bundleDirectoryName) {
                $url = 'bundles/' . $bundleDirectoryName . '/' . $filename;

                return $package->getUrl($url);
            }, $this->getAdministrationScripts($bundle));

            $baseUrl = $this->getBaseUrl($bundle, $package, $bundleDirectoryName);

            if (empty($styles) && empty($scripts)) {
                if ($baseUrl === null) {
                    continue;
                }
            }

            $assets[$bundle->getName()] = [
                'css' => $styles,
                'js' => $scripts,
                'baseUrl' => $baseUrl,
                'type' => 'plugin',
            ];
        }

        return $assets;
    }

    /**
     * @return list<string>
     */
    private function getAdministrationStyles(Bundle $bundle): array
    {
        $path = 'administration/css/' . str_replace('_', '-', (string) $bundle->getContainerPrefix()) . '.css';
        $bundlePath = $bundle->getPath();

        if (!file_exists($bundlePath . '/Resources/public/' . $path) && !file_exists($bundlePath . '/Resources/.administration-css')) {
            return [];
        }

        return [$path];
    }

    /**
     * @return list<string>
     */
    private function getAdministrationScripts(Bundle $bundle): array
    {
        $path = 'administration/js/' . str_replace('_', '-', (string) $bundle->getContainerPrefix()) . '.js';
        $bundlePath = $bundle->getPath();

        if (!file_exists($bundlePath . '/Resources/public/' . $path) && !file_exists($bundlePath . '/Resources/.administration-js')) {
            return [];
        }

        return [$path];
    }

    private function getBaseUrl(Bundle $bundle, PackageInterface $package, string $bundleDirectoryName): ?string
    {
        if (!$bundle instanceof Plugin) {
            return null;
        }

        if ($bundle->getAdminBaseUrl()) {
            return $bundle->getAdminBaseUrl();
        }

        $defaultEntryFile = 'administration/index.html';
        $bundlePath = $bundle->getPath();

        if (!file_exists($bundlePath . '/Resources/public/' . $defaultEntryFile)) {
            return null;
        }

        $url = 'bundles/' . $bundleDirectoryName . '/' . $defaultEntryFile;

        return $package->getUrl($url);
    }
}
