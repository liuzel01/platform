<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Store\Service;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Store\Exception\CanNotDownloadPluginManagedByComposerException;
use Shuwei\Core\Framework\Store\Exception\StoreNotAvailableException;
use Shuwei\Core\Framework\Store\Services\ExtensionDownloader;
use Shuwei\Core\Framework\Store\StoreException;
use Shuwei\Core\Framework\Test\Store\StoreClientBehaviour;
use Shuwei\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shuwei\Core\Framework\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class ExtensionDownloaderTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StoreClientBehaviour;

    /**
     * @var ExtensionDownloader
     */
    private $extensionDownloader;

    protected function setUp(): void
    {
        $this->extensionDownloader = $this->getContainer()->get(ExtensionDownloader::class);

        @mkdir($this->getContainer()->getParameter('kernel.app_dir'), 0777, true);
    }

    public function testDownloadExtension(): void
    {
        $this->getRequestHandler()->reset();
        $this->getRequestHandler()->append(new Response(200, [], '{"location": "http://localhost/my.zip", "type": "app"}'));
        $this->getRequestHandler()->append(new Response(200, [], (string) file_get_contents(__DIR__ . '/../_fixtures/TestApp.zip')));

        $context = $this->createAdminStoreContext();

        $this->extensionDownloader->download('TestApp', $context);
        $expectedLocation = $this->getContainer()->getParameter('kernel.app_dir') . '/TestApp';

        static::assertFileExists($expectedLocation);
        (new Filesystem())->remove($expectedLocation);
    }

    public function testDownloadExtensionServerNotReachable(): void
    {
        $this->getRequestHandler()->reset();
        $this->getRequestHandler()->append(new Response(200, [], '{"location": "http://localhost/my.zip"}'));
        $this->getRequestHandler()->append(new Response(500, [], ''));

        $context = $this->createAdminStoreContext();

        static::expectException(StoreNotAvailableException::class);
        $this->extensionDownloader->download('TestApp', $context);
    }

    public function testDownloadWhichIsAnComposerExtension(): void
    {
        if (Feature::isActive('v6.6.0.0')) {
            static::expectException(StoreException::class);
        } else {
            static::expectException(CanNotDownloadPluginManagedByComposerException::class);
        }

        $this->getContainer()->get('plugin.repository')->create(
            [
                [
                    'name' => 'TestApp',
                    'label' => 'TestApp',
                    'baseClass' => 'TestApp',
                    'path' => $this->getContainer()->getParameter('kernel.project_dir') . '/vendor/swag/TestApp',
                    'autoload' => [],
                    'version' => '1.0.0',
                    'managedByComposer' => true,
                ],
            ],
            Context::createDefaultContext()
        );

        $this->extensionDownloader->download('TestApp', Context::createDefaultContext(new AdminApiSource(Uuid::randomHex())));
    }

    public function testDownloadExtensionWhichIsALocalComposerPlugin(): void
    {
        $this->getRequestHandler()->reset();
        $this->getRequestHandler()->append(new Response(200, [], '{"location": "http://localhost/my.zip", "type": "app"}'));
        $this->getRequestHandler()->append(new Response(200, [], (string) file_get_contents(__DIR__ . '/../_fixtures/TestApp.zip')));

        $pluginPath = $this->getContainer()->getParameter('kernel.plugin_dir') . '/TestApp';
        $projectPath = $this->getContainer()->getParameter('kernel.project_dir');

        $this->getContainer()->get('plugin.repository')->create(
            [
                [
                    'name' => 'TestApp',
                    'label' => 'TestApp',
                    'baseClass' => 'TestApp',
                    'path' => str_replace($projectPath . '/', '', $pluginPath),
                    'autoload' => [],
                    'version' => '1.0.0',
                    'managedByComposer' => true,
                ],
            ],
            Context::createDefaultContext()
        );

        $context = $this->createAdminStoreContext();

        $this->extensionDownloader->download('TestApp', $context);
        $expectedLocation = $this->getContainer()->getParameter('kernel.app_dir') . '/TestApp';

        static::assertFileExists($expectedLocation);
        (new Filesystem())->remove($expectedLocation);
    }
}
