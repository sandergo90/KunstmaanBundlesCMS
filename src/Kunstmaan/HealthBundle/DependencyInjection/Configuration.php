<?php

namespace Kunstmaan\HealthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_health');
        $rootNode
            ->children()
                ->scalarNode('caching')->defaultFalse()->info('If set to true, URL checks are cached to prevent accessing the same URL multiple times in short order.')->end()
                ->arrayNode('bundles')
                    ->defaultValue(array())
                    ->normalizeKeys(false)
                    ->info('The list of bundles to manage in the health zone')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
