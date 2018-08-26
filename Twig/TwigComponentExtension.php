<?php

namespace Olveneer\TwigComponentsBundle\Twig;

use Olveneer\TwigComponentsBundle\Service\ConfigStore;
use Olveneer\TwigComponentsBundle\Service\TwigComponentKernel;
use Twig\Extension\AbstractExtension as BaseTwigExtension;
use Twig\TwigFunction;

/**
 * Class TwigComponentExtension
 * @package App\Olveneer\TwigComponentsBundle\Service
 */
class TwigComponentExtension extends BaseTwigExtension
{

    /**
     * @var TwigComponentKernel
     */
    private $kernel;

    /**
     * @var ConfigStore
     */
    private $configStore;

    /**
     * TwigComponentExtension constructor.
     * @param TwigComponentKernel $twigComponentKernel
     * @param string $renderFunction
     * @param string $accessFunction
     */
    public function __construct(TwigComponentKernel $twigComponentKernel, ConfigStore $configStore)
    {
        $this->kernel = $twigComponentKernel;
        $this->configStore = $configStore;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        $isSafe = ['is_safe' => ['html']];
        return [

            /** Renders a component based on its name and props */
            new TwigFunction($this->configStore->renderFunction, function ($name, $props = []) {
                return $this->kernel->renderComponent($name, $props);
            }, $isSafe),

            /** Returns one or more parameters from a component */
            new TwigFunction($this->configStore->accessFunction, function ($name, $requestedParameters, $props = []) {

                $component = $this->kernel->getComponent($name, $props);
                $parameters = $component->getParameters($props);

                $getParameter = function ($parameter) use ($parameters) {
                    if (isset($parameters[$parameter])) {
                        return $parameters[$parameter];
                    } else {
                        return null;
                    }
                };

                if (gettype($requestedParameters) == 'array') {
                    $fetchedParameters = [];

                    foreach ($requestedParameters as $parameter) {
                        $fetchedParameters[$parameter] = $getParameter($parameter);
                    }

                    return $fetchedParameters;
                } else {
                    return $getParameter($requestedParameters);
                }
            }, $isSafe)

        ];
    }
}
