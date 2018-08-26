<?php

namespace Olveneer\TwigComponentsBundle\Service;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TwigComponentPass
 * @package App\Olveneer\TwigComponentsBundle\Service
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

        $kernel = $container->findDefinition(TwigComponentKernel::class);

        
        $taggedServices = $container->findTaggedServiceIds('olveneer.component');

        foreach ($taggedServices as $id => $tags) {
            $kernel->addMethodCall('register', [new Reference($id)]);
        }
    }
}
