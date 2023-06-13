<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Plugin;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Framework\Plugin\KernelPluginCollection;
use Shuwei\Core\Framework\Plugin\PluginExtractor;
use Shuwei\Core\Framework\Plugin\Util\ZipUtils;
use Shuwei\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class PluginExtractorTest extends TestCase
{
    use KernelTestBehaviour;

    /**
     * @var KernelPluginCollection
     */
    protected $container;

    /**
     * @var Filesystem
     */
    private $filesystem;

    private PluginExtractor $extractor;

    protected function setUp(): void
    {
        $this->container = $this->getContainer();
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->extractor = new PluginExtractor(['plugin' => __DIR__ . '/_fixture/plugins'], $this->filesystem);

        $this->filesystem->copy(__DIR__ . '/_fixture/archives/SwagFashionTheme.zip', __DIR__ . '/_fixture/SwagFashionTheme.zip');
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove(__DIR__ . '/_fixture/plugins/SwagFashionTheme');
    }

    public function testExtractPlugin(): void
    {
        $archive = ZipUtils::openZip(__DIR__ . '/_fixture/SwagFashionTheme.zip');

        $this->extractor->extract($archive, false, 'plugin');

        $extractedPlugin = $this->filesystem->exists(__DIR__ . '/_fixture/plugins/SwagFashionTheme');
        $extractedPluginBaseClass = $this->filesystem->exists(__DIR__ . '/_fixture/plugins/SwagFashionTheme/SwagFashionTheme.php');
        static::assertTrue($extractedPlugin);
        static::assertTrue($extractedPluginBaseClass);
    }
}
