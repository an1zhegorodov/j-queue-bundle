<?php

namespace An1zhegorodov\JQueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('j_queue');
        $rootNode
            ->children()
                ->arrayNode('database')
                    ->children()
                        ->scalarNode('dsn')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('table')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('user')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('password')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('job_types')
                    ->defaultValue(array())
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->integerNode('id')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                ->ifInArray(array(JQueueExtension::DEFAULT_JOB_ID))
                                    ->thenInvalid(sprintf('Id "%d" is default job id. Please choose another value', JQueueExtension::DEFAULT_JOB_ID))
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

        return $treeBuilder;
    }
}
