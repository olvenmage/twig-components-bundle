<?php

namespace Olveneer\TwigComponentsBundle\DependencyInjection;


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
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);
        $templatingOptions = $config['templating'];

        $kernelDefinition = $container->getDefinition('olveneer.component_kernel');
        $kernelDefinition->replaceArgument(2, $config['components_directory']);

        $loaderDefinition = $container->getDefinition('olveneer.component_loader');
        $loaderDefinition->replaceArgument(1, $templatingOptions['render_function']);
        $loaderDefinition->replaceArgument(2, $templatingOptions['access_function']);
    }
}
