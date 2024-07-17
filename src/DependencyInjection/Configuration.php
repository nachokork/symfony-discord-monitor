<?php

namespace App\ErrorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('app_error');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->scalarNode('webhook_url')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}