<?php

namespace Olveneer\TwigComponentsBundle\Resources\Twig;

use Twig\Extension\AbstractExtension as BaseTwigExtension;
use Twig\TwigFunction;

/**
 * Class TwigComponentExtension
 * @package App\Olveneer\TwigComponentsBundle\Resources\Service
 */
class TwigComponentExtension extends BaseTwigExtension
{

    /**
     * @var TwigComponentKernel
     */
    private $kernel;

    /**
     * @var string
     */
    private $renderFunction;

    /**
     * @var string
     */
    private $accessFunction;

    /**
     * TwigComponentExtension constructor.
     * @param TwigComponentKernel $twigComponentKernel
     * @param string $renderFunction
     * @param string $accessFunction
     */
    public function __construct(TwigComponentKernel $twigComponentKernel, $renderFunction = 'component', $accessFunction = 'access')
    {
        $this->kernel = $twigComponentKernel;
        $this->renderFunction = $renderFunction;
        $this->accessFunction = $accessFunction;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        $isSafe = ['is_safe' => ['html']];
        return [

            /** Renders a component based on its name and props */
            new TwigFunction($this->renderFunction, function ($name, $props = []) {
                return $this->kernel->renderComponent($name, $props);
            }, $isSafe),

            /** Returns one or more parameters from a component */
            new TwigFunction($this->accessFunction, function ($name, $requestedParameters, $props = []) {
                $parameters = $this->kernel->getComponentParameters($name, $props);

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
