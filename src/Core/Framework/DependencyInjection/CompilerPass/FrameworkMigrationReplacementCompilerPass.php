<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DependencyInjection\CompilerPass;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Migration\MigrationSource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('core')]
class FrameworkMigrationReplacementCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $bundleRoot = \dirname(__DIR__, 3);

        $migrationSourceV5 = $container->getDefinition(MigrationSource::class . '.core.V6_5');
        $migrationSourceV5->addMethodCall('addDirectory', [$bundleRoot . '/Migration/V6_5', 'Shuwei\Core\Migration\V6_5']);

        $migrationSourceV6 = $container->getDefinition(MigrationSource::class . '.core.V6_6');
        $migrationSourceV6->addMethodCall('addDirectory', [$bundleRoot . '/Migration/V6_6', 'Shuwei\Core\Migration\V6_6']);
    }
}
