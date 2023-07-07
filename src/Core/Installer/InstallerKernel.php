<?php declare(strict_types=1);

namespace Shuwei\Core\Installer;

use Composer\InstalledVersions;
use Shuwei\Core\DevOps\Environment\EnvironmentHelper;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\VersionParser;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @internal
 */
#[Package('core')]
class InstallerKernel extends HttpKernel
{
    use MicroKernelTrait;

    private readonly string $shuweiVersion;

    private readonly ?string $shuweiVersionRevision;

    public function __construct(
        string $environment,
        bool $debug
    ) {
        parent::__construct($environment, $debug);

        // @codeCoverageIgnoreStart - not testable, as static calls cannot be mocked
        if (InstalledVersions::isInstalled('58shuwei/platform')) {
            $version = InstalledVersions::getVersion('58shuwei/platform')
                . '@' . InstalledVersions::getReference('58shuwei/platform');
        } else {
            $version = InstalledVersions::getVersion('58shuwei/core')
                . '@' . InstalledVersions::getReference('58shuwei/core');
        }
        // @codeCoverageIgnoreEnd

        $version = VersionParser::parseShuweiVersion($version);
        $this->shuweiVersion = $version['version'];
        $this->shuweiVersionRevision = $version['revision'];
    }

    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        parent::boot();
        $this->ensureComposerHomeVarIsSet();
    }

    /**
     * @return \Generator<BundleInterface>
     */
    public function registerBundles(): \Generator
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new Installer();
    }

    public function getProjectDir(): string
    {
        $r = new \ReflectionObject($this);

        /** @var string $dir */
        $dir = $r->getFileName();
        if (!file_exists($dir)) {
            throw new \LogicException(sprintf('Cannot auto-detect project dir for kernel of class "%s".', $r->name));
        }

        $dir = $rootDir = \dirname($dir);
        while (!file_exists($dir . '/vendor')) {
            if ($dir === \dirname($dir)) {
                return $rootDir;
            }
            $dir = \dirname($dir);
        }

        return $dir;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    protected function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();

        return array_merge(
            $parameters,
            [
                'kernel.shuwei_version' => $this->shuweiVersion,
                'kernel.shuwei_version_revision' => $this->shuweiVersionRevision,
                'kernel.secret' => 'noSecr3t',
            ]
        );
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        // use hard coded default config for loaded bundles
        $loader->load(__DIR__ . '/../Framework/Resources/config/packages/installer.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__ . '/Resources/config/routes.xml');
    }

    /**
     * We check the requirements via composer, and composer will fail if the composer home is not set
     */
    private function ensureComposerHomeVarIsSet(): void
    {
        if (!EnvironmentHelper::getVariable('COMPOSER_HOME')) {
            // The same location is also used in EnvConfigWriter and SystemSetupCommand
            $fallbackComposerHome = $this->getProjectDir() . '/var/cache/composer';
            $_ENV['COMPOSER_HOME'] = $_SERVER['COMPOSER_HOME'] = $fallbackComposerHome;
        }
    }
}
