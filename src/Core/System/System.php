<?php declare(strict_types=1);

namespace Shuwei\Core\System;

use Shuwei\Core\Framework\Bundle;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\System\CustomEntity\CustomEntityRegistrar;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
#[Package('core')]
class System extends Bundle
{
    public function getTemplatePriority(): int
    {
        return -1;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('custom_entity.xml');
        $loader->load('locale.xml');
        $loader->load('snippet.xml');
        $loader->load('user.xml');
        $loader->load('configuration.xml');
    }

    public function boot(): void
    {
        parent::boot();

        $this->container->get(CustomEntityRegistrar::class)->register();
    }
}
