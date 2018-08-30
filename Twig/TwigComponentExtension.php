<?php

namespace Olveneer\TwigComponentsBundle\Twig;

use Olveneer\TwigComponentsBundle\Service\ConfigStore;
use Olveneer\TwigComponentsBundle\Service\ComponentRenderer;
use Twig\Extension\AbstractExtension as BaseTwigExtension;
use Twig\TwigFunction;

/**
 * Class TwigComponentExtension
 * @package App\Olveneer\TwigComponentsBundle\Service
 */
class TwigComponentExtension extends BaseTwigExtension
{

    /**
     * @var ComponentRenderer
     */
    private $renderer;

    /**
     * @var ConfigStore
     */
    private $configStore;

    /**
     * TwigComponentExtension constructor.
     * @param ComponentRenderer $componentRenderer
     * @param ConfigStore $configStore
     */
    public function __construct(ComponentRenderer $componentRenderer, ConfigStore $configStore)
    {
        $this->renderer = $componentRenderer;
        $this->configStore = $configStore;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [

            /** Returns one or more parameters from a component */
            new TwigFunction('access', function ($name, $requestedParameters, $props = []) {

                $component = $this->renderer->getComponent($name, $props);
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
            }, ['is_safe' => ['html', 'deprecated' => true]])

        ];
    }
}
