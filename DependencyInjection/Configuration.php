<?php

namespace Koala\Bundle\MercuryContentBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('koala_mercury_content');

        $saveMethods = array('put', 'post');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('role')->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                ->scalarNode('save_method')
                    ->defaultValue('put')
                    ->validate()
                        ->ifNotInArray($saveMethods)
                        ->thenInvalid('Invalid saveMethod: %s. Please choose one of '.json_encode($saveMethods))
                    ->end()
                ->end()
                ->arrayNode('upload')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('path')->defaultValue('%kernel.root_dir%/../web/upload')->end()
                        ->scalarNode('url')->defaultValue('upload')->end()
                        ->booleanNode('absolute')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
