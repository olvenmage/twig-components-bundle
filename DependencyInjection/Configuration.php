<?php

namespace Olveneer\TwigComponentsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration  implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('twig_components');

        $rootNode
            ->children()
            ->variableNode('components_directory')->defaultValue('/components')->end()
            ->end()
        ;

        return $treeBuilder;
    }

}