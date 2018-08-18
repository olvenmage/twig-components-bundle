<?php

namespace Olveneer\TwigComponentsBundle\Resources\src;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TwigComponentPass
 * @package App\Olveneer\TwigComponentsBundle\Resources\src
 */
class TwigComponentPass implements CompilerPassInterface
{
    
    /**
     * Registers all services with the 'olveneer.component' tag as valid components.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(TwigComponentStore::class)) {
            return;
        }

        $definition = $container->findDefinition(TwigComponentStore::class);
        
        $taggedServices = $container->findTaggedServiceIds('olveneer.component');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addComponent', [new Reference($id)]);
        }
    }
    
}