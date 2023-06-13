<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DependencyInjection\CompilerPass;

use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[Package('core')]
class FilesystemConfigMigrationCompilerPass implements CompilerPassInterface
{
    private const MIGRATED_FS = [ 'asset', 'sitemap'];

    public function process(ContainerBuilder $container): void
    {
        foreach (self::MIGRATED_FS as $fs) {
            $key = sprintf('shuwei.filesystem.%s', $fs);
            $urlKey = $key . '.url';
            $typeKey = $key . '.type';
            $configKey = $key . '.config';
            if ($container->hasParameter($typeKey)) {
                continue;
            }

            // 6.1 always refers to the main shop url on theme, asset and sitemap.
            $container->setParameter($urlKey, '');
            $container->setParameter($key, '%shuwei.filesystem.public%');
            $container->setParameter($typeKey, '%shuwei.filesystem.public.type%');
            $container->setParameter($configKey, '%shuwei.filesystem.public.config%');
        }
    }
}
