<?php declare(strict_types=1);

namespace Shuwei\Core\Content;

use Shuwei\Core\Framework\Bundle;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
#[Package('core')]
class Content extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/DependencyInjection/'));
        $loader->load('media.xml');
        if ($container->getParameter('kernel.environment') === 'test') {
            $loader->load('services_test.xml');
        }
    }
}
