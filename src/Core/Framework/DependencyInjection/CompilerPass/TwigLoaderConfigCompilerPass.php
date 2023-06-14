<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DependencyInjection\CompilerPass;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

#[Package('core')]
class TwigLoaderConfigCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $fileSystemLoader = $container->findDefinition('twig.loader.native_filesystem');

        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
        if (!\is_array($bundlesMetadata)) {
            throw new \RuntimeException('Container parameter "kernel.bundles_metadata" needs to be an array');
        }

        foreach ($bundlesMetadata as $name => $bundle) {
            $viewDirectory = $bundle['path'] . '/Resources/views';
            $resourcesDirectory = $bundle['path'] . '/Resources';

            if (file_exists($viewDirectory)) {
                $fileSystemLoader->addMethodCall('addPath', [$viewDirectory]);
                $fileSystemLoader->addMethodCall('addPath', [$viewDirectory, $name]);
            }
            if (file_exists($resourcesDirectory)) {
                $fileSystemLoader->addMethodCall('addPath', [$resourcesDirectory, $name]);
            }
        }
    }
}
