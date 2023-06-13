<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Plugin;

use Shuwei\Core\Framework\Bundle;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin;
use Shuwei\Core\Kernel;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

#[Package('core')]
class BundleConfigGenerator implements BundleConfigGeneratorInterface
{
    private readonly string $projectDir;

    /**
     * @internal
     */
    public function __construct(
        private readonly Kernel $kernel,
    ) {
        $projectDir = $this->kernel->getContainer()->getParameter('kernel.project_dir');
        if (!\is_string($projectDir)) {
            throw new \RuntimeException('Container parameter "kernel.project_dir" needs to be a string');
        }
        $this->projectDir = $projectDir;
    }

    public function getConfig(): array
    {
        return array_merge($this->generatePluginConfigs(), $this->generateAppConfigs());
    }

    private function generatePluginConfigs(): array
    {
        $activePlugins = $this->getActivePlugins();

        $kernelBundles = $this->kernel->getBundles();

        $bundles = [];
        foreach ($kernelBundles as $bundle) {
            // only include shuwei bundles
            if (!$bundle instanceof Bundle) {
                continue;
            }

            // dont include deactivated plugins
            if ($bundle instanceof Plugin && !\in_array($bundle->getName(), $activePlugins, true)) {
                continue;
            }

            $path = $bundle->getPath();
            if (mb_strpos($bundle->getPath(), $this->projectDir) === 0) {
                // make relative
                $path = \ltrim(\mb_substr($path, \mb_strlen($this->projectDir)), '/');
            }

            $bundles[$bundle->getName()] = [
                'basePath' => $path . '/',
                'views' => ['Resources/views'],
                'technicalName' => \str_replace('_', '-', $bundle->getContainerPrefix()),
                'administration' => [
                    'path' => 'Resources/app/administration/src',
                    'entryFilePath' => $this->getEntryFile($bundle->getPath(), 'Resources/app/administration/src'),
                    'webpack' => $this->getWebpackConfig($bundle->getPath(), 'Resources/app/administration'),
                ],
            ];
        }

        return $bundles;
    }

    private function getEntryFile(string $rootPath, string $componentPath): ?string
    {
        $path = trim($componentPath, '/');
        $absolutePath = $rootPath . '/' . $path;

        return file_exists($absolutePath . '/main.ts') ? $path . '/main.ts'
            : (file_exists($absolutePath . '/main.js') ? $path . '/main.js'
            : null);
    }

    private function getWebpackConfig(string $rootPath, string $componentPath): ?string
    {
        $path = trim($componentPath, '/');
        $absolutePath = $rootPath . '/' . $path;

        if (!file_exists($absolutePath . '/build/webpack.config.js')) {
            return null;
        }

        if (mb_strpos($path, $this->projectDir) === 0) {
            // make relative
            $path = ltrim(mb_substr($path, mb_strlen($this->projectDir)), '/');
        }

        return $path . '/build/webpack.config.js';
    }

    private function asSnakeCase(string $string): string
    {
        return (new CamelCaseToSnakeCaseNameConverter())->normalize($string);
    }

    private function getActivePlugins(): array
    {
        $activePlugins = $this->kernel->getPluginLoader()->getPluginInstances()->getActives();

        return array_map(static fn (Plugin $plugin) => $plugin->getName(), $activePlugins);
    }
}
