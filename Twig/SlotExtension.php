<?php

namespace Olveneer\TwigComponentsBundle\Twig;

use Olveneer\TwigComponentsBundle\Service\ComponentRenderer;
use Olveneer\TwigComponentsBundle\Twig\tag\ComponentParser;
use Olveneer\TwigComponentsBundle\Twig\tag\CollectParser;

/**
 * Class SlotExtension
 * @package Olveneer\TwigComponentsBundle\Twig
 */
class SlotExtension extends \Twig_Extension
{
    /**
     * @var ComponentRenderer
     */
    private $renderer;

    /**
     * TwigComponentExtension constructor.
     * @param ComponentRenderer $componentRenderer
     */
    public function __construct(ComponentRenderer $componentRenderer)
    {
        $this->renderer = $componentRenderer;
    }

    /**
     * @return array|\Twig_TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return [new ComponentParser(), new CollectParser()];
    }

    /**
     * @return ComponentRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return \Twig_Compiler
     */
    public function createCompiler()
    {
        return new \Twig_Compiler($this->renderer->getEnv());
    }

}