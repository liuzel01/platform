<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DependencyInjection;

use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Util\MemorySizeCalculator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

#[Package('core')]
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('shuwei');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->append($this->createApiSection())
                ->append($this->createFeatureSection())
                ->append($this->createDalSection())
                ->append($this->createFilesystemSection())
                ->append($this->createIncrementSection())
                ->append($this->createAdminWorkerSection())
                ->append($this->createTwigSection())
                ->append($this->createCacheSection())
                ->append($this->createHtmlSanitizerSection())
                ->append($this->createAutoUpdateSection())
                ->append($this->createStoreSection())
                ->append($this->createProfilerSection())
                ->append($this->createLoggerSection())
            ->end();

        return $treeBuilder;
    }
    private function createLoggerSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('logger'))->getRootNode();
        $rootNode
            ->children()
                ->integerNode('file_rotation_count')
                    ->defaultValue(14)
                ->end()
                ->arrayNode('exclude_exception')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('exclude_events')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('error_code_log_levels')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $rootNode;
    }
    private function createProfilerSection(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('profiler');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('integrations')
                    ->performNoDeepMerging()
                    ->scalarPrototype()
                ->end()
            ->end();

        return $rootNode;
    }
    private function createStoreSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('store'))->getRootNode();
        $rootNode
            ->children()
                ->booleanNode('frw')->end()
            ->end();

        return $rootNode;
    }
    private function createAutoUpdateSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('auto_update'))->getRootNode();
        $rootNode
            ->children()
                ->booleanNode('enabled')->end()
            ->end();

        return $rootNode;
    }
    private function createHtmlSanitizerSection(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('html_sanitizer');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->variableNode('cache_dir')
                    ->defaultValue('%kernel.cache_dir%')
                ->end()
                ->booleanNode('cache_enabled')
                    ->defaultTrue()
                ->end()
                ->arrayNode('sets')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->arrayNode('tags')
                                ->defaultValue([])
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('attributes')
                                ->defaultValue([])
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('custom_attributes')
                                ->defaultValue([])
                                ->arrayPrototype()
                                    ->children()
                                        ->arrayNode('tags')
                                            ->defaultValue([])
                                            ->scalarPrototype()->end()
                                        ->end()
                                        ->arrayNode('attributes')
                                            ->defaultValue([])
                                            ->scalarPrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->useAttributeAsKey('key')
                                ->defaultValue([])
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('key')->end()
                                        ->scalarNode('value')->end()
                                        ->arrayNode('values')
                                            ->defaultValue([])
                                            ->scalarPrototype()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('fields')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->arrayNode('sets')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
    private function createCacheSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('cache'))->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('redis_prefix')->end()
                ->booleanNode('cache_compression')->defaultTrue()->end()
                ->arrayNode('invalidation')
                    ->children()
                        ->integerNode('delay')
                            ->defaultValue(0)
                        ->end()
                        ->integerNode('count')
                            ->defaultValue(150)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
    private function createTwigSection(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('twig');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('allowed_php_functions')
                    ->performNoDeepMerging()
                    ->scalarPrototype()
                ->end()
            ->end();

        return $rootNode;
    }
    private function createAdminWorkerSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('admin_worker'))->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('transports')
                    ->prototype('scalar')->end()
                ->end()
                ->integerNode('poll_interval')
                    ->defaultValue(20)
                ->end()
                ->booleanNode('enable_admin_worker')
                    ->defaultValue(true)
                ->end()
                ->booleanNode('enable_queue_stats_worker')
                    ->defaultValue(true)
                ->end()
                ->booleanNode('enable_notification_worker')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('memory_limit')
                    ->defaultValue('128M')
                ->end()
            ->end();

        return $rootNode;
    }
    private function createIncrementSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('increment'))->getRootNode();
        $rootNode
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('type')->end()
                    ->variableNode('config')->end()
                ->end()
            ->end();

        return $rootNode;
    }
    private function createFilesystemSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('filesystem'))->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('private')
                    ->children()
                        ->scalarNode('type')->end()
                        ->scalarNode('visibility')->end()
                        ->variableNode('config')->end()
                    ->end()
                ->end()
                ->arrayNode('public')
                    ->performNoDeepMerging()
                    ->children()
                        ->scalarNode('type')->end()
                        ->scalarNode('url')->end()
                        ->scalarNode('visibility')->end()
                        ->variableNode('config')->end()
                    ->end()
                ->end()
                ->arrayNode('temp')
                    ->performNoDeepMerging()
                    ->children()
                        ->scalarNode('type')->end()
                        ->scalarNode('visibility')->end()
                        ->variableNode('config')->end()
                    ->end()
                ->end()
                ->arrayNode('asset')
                    ->performNoDeepMerging()
                    ->children()
                        ->scalarNode('type')->end()
                        ->scalarNode('url')->end()
                        ->scalarNode('visibility')->end()
                        ->variableNode('config')->end()
                    ->end()
                ->end()
                ->arrayNode('sitemap')
                    ->performNoDeepMerging()
                    ->children()
                        ->scalarNode('type')->end()
                        ->scalarNode('url')->end()
                        ->scalarNode('visibility')->end()
                        ->variableNode('config')->end()
                    ->end()
                ->end()
                ->arrayNode('allowed_extensions')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('private_allowed_extensions')
                    ->prototype('scalar')->end()
                ->end()
                ->enumNode('private_local_download_strategy')
                    ->defaultValue('php')
                    ->values(['php', 'x-sendfile', 'x-accel'])
                ->end()
            ->end();

        return $rootNode;
    }

    private function createDalSection(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('dal');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->integerNode('batch_size')
                    ->min(1)
                    ->defaultValue(125)
                ->end()
                ->integerNode('max_rule_prices')
                    ->min(1)
                    ->defaultValue(100)
                ->end()
                ->arrayNode('versioning')
                    ->children()
                        ->integerNode('expire_days')
                            ->min(1)
                            ->defaultValue(30)
                            ->end()
                ->end()
            ->end();

        return $rootNode;
    }
    private function createApiSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('api'))->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('rate_limiter')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->booleanNode('enabled')->defaultTrue()->end()
                            ->scalarNode('lock_factory')->defaultValue('lock.factory')->end()
                            ->scalarNode('policy')->end()
                            ->scalarNode('limit')->end()
                            ->scalarNode('cache_pool')->defaultValue('cache.rate_limiter')->end()
                            ->scalarNode('interval')->end()
                            ->scalarNode('reset')->end()
                            ->arrayNode('rate')
                                ->children()
                                    ->scalarNode('interval')->end()
                                    ->integerNode('amount')->defaultValue(1)->end()
                                ->end()
                            ->end()
                            ->variableNode('limits')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('store')
                    ->children()
                    ->scalarNode('context_lifetime')->defaultValue('P1D')->end()
                ->end()
            ->end()
            ->scalarNode('access_token_ttl')->defaultValue('PT10M')->end()
            ->scalarNode('refresh_token_ttl')->defaultValue('P1W')->end()
            ->arrayNode('jwt_key')
                ->children()
                    ->scalarNode('private_key_path')->end()
                    ->scalarNode('private_key_passphrase')->defaultValue('shuwei')->end()
                    ->scalarNode('public_key_path')->end()
                ->end()
            ->end()
            ->integerNode('max_limit')->end()
            ->arrayNode('api_browser')
                ->children()
                ->booleanNode('auth_required')
                    ->defaultTrue()
                ->end()
            ->end();

        return $rootNode;
    }

    private function createFeatureSection(): ArrayNodeDefinition
    {
        $rootNode = (new TreeBuilder('feature'))->getRootNode();
        $rootNode
            ->children()
            ->arrayNode('flags')
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')->end()
                        ->booleanNode('default')->defaultFalse()->end()
                        ->booleanNode('major')->defaultFalse()->end()
                        ->scalarNode('description')->end()
                    ->end()
                ->end()
                ->beforeNormalization()
                    ->always()->then(function ($flags) {
                        foreach ($flags as $key => $flag) {
                            // support old syntax
                            if (\is_int($key) && \is_string($flag)) {
                                unset($flags[$key]);

                                $flags[] = [
                                    'name' => $flag,
                                ];
                            }
                        }

                        return $flags;
                    })
                    ->end()
            ->end();

        return $rootNode;
    }
}
