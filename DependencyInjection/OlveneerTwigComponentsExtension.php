<?php

namespace Olveneer\TwigComponentsBundle\DependencyInjection;

use Olveneer\TwigComponentsBundle\Component\TwigComponentInterface;
use Olveneer\TwigComponentsBundle\Component\TwigComponentMixin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class OlveneerTwigComponentsExtension
 * @package Olveneer\TwigComponentsBundle\DependencyInjection
 */
class OlveneerTwigComponentsExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $configDefinition = $container->getDefinition('olveneer.config_store');
        $configDefinition->replaceArgument(0, $config['components_directory']);

        $container->registerForAutoconfiguration(TwigComponentMixin::class)
            ->addTag('olveneer.mixin');

        $container->registerForAutoconfiguration(TwigComponentInterface::class)
            ->addTag('olveneer.component');
    }
}
