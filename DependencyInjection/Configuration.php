<?php

namespace An1zhegorodov\JQueueBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jqueue');
        $rootNode
            ->children()
                ->arrayNode('job_types')
                    ->defaultValue(array())
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->integerNode('id')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                ->ifInArray(array(99999999))
                                    ->thenInvalid('Id "99999999" is not allowed, please choose another value')
                                ->end()
                            ->end()
                            ->scalarNode('title')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                ->ifTrue(function($v) { return !is_string($v); })
                                    ->thenInvalid('Title should be string')
                                ->end()
                                ->validate()
                                ->ifTrue(function($v) { return strtolower($v) === 'default'; })
                                    ->thenInvalid('Title "default" is not allowed, please choose another value')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function($v) { return count($v) !== count(array_unique(array_column($v, 'id'))); })
                            ->thenInvalid('Each job type should have unique identifier')
                    ->end()
                    ->validate()
                        ->ifTrue(function($v) { return count($v) !== count(array_unique(array_column($v, 'title'))); })
                            ->thenInvalid('Each job type should have unique title')
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
