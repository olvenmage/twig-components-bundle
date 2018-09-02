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
     * Access a component's parameters.
     *
     * @deprecated Use slots with the exposing of parameters instead, or, directly inject them or use a Mixin.
     *  Will be removed later.
     *
     * @param $name
     * @param $requestedParameters
     * @param array $props
     * @return array|null
     */
    public function access($name, $requestedParameters, $props = [])
    {
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
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [

            /** Returns one or more parameters from a component */
            new TwigFunction('access', [$this, 'access'], ['is_safe' => ['html'], 'deprecated' => true])

        ];
    }
}
