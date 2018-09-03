<?php

namespace Olveneer\TwigComponentsBundle\Service;

use Olveneer\TwigComponentsBundle\Component\TwigComponent;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ComponentPass
 * @package App\Olveneer\TwigComponentsBundle\Service
 */
class ComponentPass implements CompilerPassInterface
{
    
    /**
     * Registers all services with the 'olveneer.component' tag as valid components.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ComponentStore::class)) {
            return;
        }

        $renderer = $container->findDefinition(ComponentRenderer::class);




        $taggedServices = $container->findTaggedServiceIds('olveneer.component');

        foreach ($taggedServices as $id => $tags) {
            $renderer->addMethodCall('register', [new Reference($id)]);
        }

        $taggedServices = $container->findTaggedServiceIds('olveneer.mixin');

        foreach ($taggedServices as $id => $tags) {
            $renderer->addMethodCall('registerMixin', [new Reference($id)]);
        }
    }
}
