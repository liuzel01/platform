<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DependencyInjection\CompilerPass;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Frontend\Theme\ThemeCompiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

#[Package('core')]
class AssetRegistrationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $assets = [];
        foreach ($container->findTaggedServiceIds('shuwei.asset') as $id => $config) {
            $container->getDefinition($id)->addTag('assets.package', ['package' => $config[0]['asset']]);
            $assets[$config[0]['asset']] = new Reference($id);
        }

        $assetService = $container->getDefinition('assets.packages');
        $assetService->addMethodCall('setDefaultPackage', [$assets['asset']]);

        if ($container->hasDefinition(ThemeCompiler::class)) {
            $container->getDefinition(ThemeCompiler::class)->replaceArgument(6, $assets);
        }
    }
}
