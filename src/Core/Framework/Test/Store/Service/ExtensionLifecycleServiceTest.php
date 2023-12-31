<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Service;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Api\Context\SystemSource;
use Shuwei\Core\Framework\App\AppCollection;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\Store\Exception\ExtensionInstallException;
use Shuwei\Core\Framework\Store\Exception\ExtensionNotFoundException;
use Shuwei\Core\Framework\Store\Exception\ExtensionThemeStillInUseException;
use Shuwei\Core\Framework\Store\Services\ExtensionLifecycleService;
use Shuwei\Core\Framework\Test\Store\ExtensionBehaviour;
use Shuwei\Core\Framework\Test\Store\StoreClientBehaviour;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 *
 * @group skip-paratest
 */
class ExtensionLifecycleServiceTest extends TestCase
{
    use ExtensionBehaviour;
    use IntegrationTestBehaviour;
    use StoreClientBehaviour;

    private ExtensionLifecycleService $lifecycleService;

    private EntityRepository $appRepository;

    private EntityRepository $pluginRepository;

    private ?EntityRepository $themeRepository;

    private EntityRepository $websiteRepository;

    private Context $context;

    protected function setUp(): void
    {
        $this->lifecycleService = $this->getContainer()->get(ExtensionLifecycleService::class);

        $this->appRepository = $this->getContainer()->get('app.repository');
        $this->pluginRepository = $this->getContainer()->get('plugin.repository');
        $this->themeRepository = $this->getContainer()->get('theme.repository', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $this->websiteRepository = $this->getContainer()->get('website.repository');
        $this->context = new Context(new SystemSource(), [], Defaults::CURRENCY, [Defaults::LANGUAGE_SYSTEM]);
    }

    protected function tearDown(): void
    {
        $this->removeApp(__DIR__ . '/../_fixtures/TestApp');
        $this->removeApp(__DIR__ . '/../_fixtures/TestAppTheme');
        $this->removePlugin(__DIR__ . '/../_fixtures/AppStoreTestPlugin');
    }

    public function testInstallExtension(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestApp', false);

        $this->lifecycleService->install('app', 'TestApp', $this->context);

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        static::assertCount(1, $apps);
        static::assertEquals('TestApp', $apps->first()->getName());
        static::assertFalse($apps->first()->isActive());
    }

    public function testUninstallWithInvalidName(): void
    {
        $this->lifecycleService->uninstall('app', 'notExisting', false, $this->context);
    }

    public function testInstallAppNotExisting(): void
    {
        static::expectException(ExtensionInstallException::class);
        $this->lifecycleService->install('app', 'notExisting', $this->context);
    }

    public function testRemoveExtension(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestApp');

        $this->lifecycleService->uninstall('app', 'TestApp', false, $this->context);
        $this->lifecycleService->remove('app', 'TestApp', $this->context);

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        static::assertCount(0, $apps);
    }

    public function testActivateExtension(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestApp');

        $this->lifecycleService->activate('app', 'TestApp', $this->context);

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        static::assertCount(1, $apps);
        static::assertEquals('TestApp', $apps->first()->getName());
        static::assertTrue($apps->first()->isActive());
    }

    public function testDeactivateExtension(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestApp');

        $this->lifecycleService->activate('app', 'TestApp', $this->context);
        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        static::assertTrue($apps->first()->isActive());

        $this->lifecycleService->deactivate('app', 'TestApp', $this->context);

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        static::assertCount(1, $apps);
        static::assertEquals('TestApp', $apps->first()->getName());
        static::assertFalse($apps->first()->isActive());
    }

    public function testUpdateExtensionNotExisting(): void
    {
        static::expectException(ExtensionInstallException::class);
        $this->lifecycleService->update('app', 'foo', false, $this->context);
    }

    public function testUpdateExtensionNotInstalled(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestApp', false);
        static::expectException(ExtensionNotFoundException::class);
        $this->lifecycleService->update('app', 'TestApp', false, $this->context);
    }

    public function testUpdateExtension(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestApp');

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        static::assertSame('1.0.0', $apps->first()->getVersion());

        $appManifestPath = $this->getContainer()->getParameter('kernel.app_dir') . '/TestApp/manifest.xml';
        file_put_contents($appManifestPath, str_replace('1.0.0', '1.0.1', file_get_contents($appManifestPath)));

        $this->lifecycleService->update('app', 'TestApp', false, $this->context);

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        static::assertSame('1.0.1', $apps->first()->getVersion());
    }

    public function testExtensionCantBeRemovedIfAThemeIsAssigned(): void
    {
        $themeRepo = $this->themeRepository;
        if (!$themeRepo) {
            static::markTestSkipped('ExtensionLifecycleServiceTest needs frontend to be installed.');
        }

        $this->installApp(__DIR__ . '/../_fixtures/TestAppTheme');
        $this->lifecycleService->activate('app', 'TestAppTheme', $this->context);

        /** @var AppCollection $apps */
        $apps = $this->appRepository->search(new Criteria(), $this->context)->getEntities();

        $theme = $themeRepo->search(
            (new Criteria())->addFilter(new EqualsFilter('technicalName', 'TestAppTheme')),
            $this->context
        )->first();

        $defaultWebsiteId = $this->websiteRepository->searchIds(new Criteria(), $this->context)
            ->firstId();

        $this->websiteRepository->update([[
            'id' => $defaultWebsiteId,
            'themes' => [
                ['id' => $theme->getId()],
            ],
        ]], $this->context);

        static::expectException(ExtensionThemeStillInUseException::class);
        $this->lifecycleService->uninstall(
            'app',
            $apps->first()->getName(),
            false,
            $this->context
        );
    }

    public function testExtensionCantBeRemovedIfAChildThemeIsAssigned(): void
    {
        $themeRepo = $this->themeRepository;
        if (!$themeRepo) {
            static::markTestSkipped('ExtensionLifecycleServiceTest needs frontend to be installed.');
        }

        $this->installApp(__DIR__ . '/../_fixtures/TestAppTheme');
        $this->lifecycleService->activate('app', 'TestAppTheme', $this->context);

        $theme = $themeRepo->search(
            (new Criteria())->addFilter(new EqualsFilter('technicalName', 'TestAppTheme')),
            $this->context
        )->first();

        $childThemeId = Uuid::randomHex();
        $themeRepo->create([[
            'id' => $childThemeId,
            'name' => 'SwagTest',
            'author' => 'Shuwei',
            'active' => true,
            'parentThemeId' => $theme->getId(),
        ]], $this->context);

        $defaultWebsiteId = $this->websiteRepository->searchIds(new Criteria(), $this->context)
            ->firstId();

        $this->websiteRepository->update([[
            'id' => $defaultWebsiteId,
            'themes' => [
                ['id' => $childThemeId],
            ],
        ]], $this->context);

        static::expectException(ExtensionThemeStillInUseException::class);
        $this->lifecycleService->uninstall(
            'app',
            'TestAppTheme',
            false,
            $this->context
        );
    }

    public function testExtensionCanBeRemovedIfThemeIsNotAssigned(): void
    {
        $themeRepo = $this->themeRepository;
        if (!$themeRepo) {
            static::markTestSkipped('ExtensionLifecycleServiceTest needs frontend to be installed.');
        }

        $this->installApp(__DIR__ . '/../_fixtures/TestAppTheme');
        $this->lifecycleService->activate('app', 'TestAppTheme', $this->context);

        $themeCriteria = new Criteria();
        $themeCriteria->addFilter(new EqualsFilter('technicalName', 'TestAppTheme'))
            ->addAssociation('websites');

        $theme = $themeRepo->search($themeCriteria, $this->context)->first();

        static::assertEquals(0, $theme->getWebsites()->count());

        $this->lifecycleService->uninstall(
            'type',
            'TestAppTheme',
            false,
            $this->context
        );

        $removedApp = $this->appRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('name', 'TestAppTheme')),
            $this->context
        )->first();

        static::assertNull($removedApp);
    }

    public function testDeleteAppWithDifferentName(): void
    {
        $this->installApp(__DIR__ . '/../_fixtures/TestAppTheme');

        $oldName = $this->getContainer()->getParameter('shuwei.app_dir') . '/TestAppTheme';
        $newName = $this->getContainer()->getParameter('shuwei.app_dir') . '/some-random-folder-name';

        rename($oldName, $newName);

        $this->lifecycleService->remove('app', 'TestAppTheme', Context::createDefaultContext());

        static::assertFileDoesNotExist($newName);
    }
}
