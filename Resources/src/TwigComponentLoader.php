<?php

namespace Olveneer\TwigComponentsBundle\Resources\src;

use Twig\Extension\AbstractExtension as Ext;
use Twig\TwigFunction;

/**
 * Class TwigComponentLoader
 * @package App\Olveneer\TwigComponentsBundle\Resources\src
 */
class TwigComponentLoader extends Ext
{
    /**
     * @var TwigComponentKernel
     */
    private $kernel;

    /**
     * TwigComponentLoader constructor.
     * @param TwigComponentKernel $twigComponentKernel
     */
    public function __construct(TwigComponentKernel $twigComponentKernel)
    {
     $this->kernel = $twigComponentKernel;
    }


    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            /** Renders a component based on its alias and props */
            new TwigFunction('component', function($alias, $props = []) {
                return $this->kernel->renderComponent($alias, $props);
            }, ['is_safe' => ['html']])
        ];
    }
}