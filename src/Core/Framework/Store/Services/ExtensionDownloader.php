<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Store\Services;

use GuzzleHttp\Exception\ClientException;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin\PluginEntity;
use Shuwei\Core\Framework\Plugin\PluginManagementService;
use Shuwei\Core\Framework\Store\Exception\CanNotDownloadPluginManagedByComposerException;
use Shuwei\Core\Framework\Store\Exception\StoreApiException;
use Shuwei\Core\Framework\Store\StoreException;
use Shuwei\Core\Framework\Store\Struct\PluginDownloadDataStruct;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
#[Package('merchant-services')]
class ExtensionDownloader
{
    private readonly string $relativePluginDir;

    public function __construct(
        private readonly EntityRepository $pluginRepository,
        private readonly StoreClient $storeClient,
        private readonly PluginManagementService $pluginManagementService,
        string $pluginDir,
        string $projectDir
    ) {
        $this->relativePluginDir = (new Filesystem())->makePathRelative($pluginDir, $projectDir);
    }

    public function download(string $technicalName, Context $context): PluginDownloadDataStruct
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('plugin.name', $technicalName));

        /** @var PluginEntity|null $plugin */
        $plugin = $this->pluginRepository->search($criteria, $context)->first();

        if ($plugin !== null && $plugin->getManagedByComposer() && !str_starts_with($plugin->getPath() ?? '', $this->relativePluginDir)) {
            if (Feature::isActive('v6.6.0.0')) {
                throw StoreException::cannotDeleteManaged($plugin->getName());
            }

            throw new CanNotDownloadPluginManagedByComposerException('can not download plugins managed by composer from store api');
        }

        try {
            $data = $this->storeClient->getDownloadDataForPlugin($technicalName, $context);
        } catch (ClientException $e) {
            throw new StoreApiException($e);
        }

        $this->pluginManagementService->downloadStorePlugin($data, $context);

        return $data;
    }
}
