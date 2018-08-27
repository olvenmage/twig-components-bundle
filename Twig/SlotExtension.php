<?php

namespace Olveneer\TwigComponentsBundle\Twig;

use Olveneer\TwigComponentsBundle\Service\TwigComponentKernel;
use Olveneer\TwigComponentsBundle\Twig\tags\component\ComponentParser;
use Olveneer\TwigComponentsBundle\Twig\tags\slot\SlotTokenParser;
use Olveneer\TwigComponentsBundle\Twig\tags\slot\SlotNodeVisitor;
use Twig\TwigFunction;

/**
 * Class SlotExtension
 * @package Olveneer\TwigComponentsBundle\Twig
 */
class SlotExtension extends \Twig_Extension
{
    /**
     * @var TwigComponentKernel
     */
    private $kernel;

    /**
     * TwigComponentExtension constructor.
     * @param TwigComponentKernel $twigComponentKernel
     * @param string $renderFunction
     * @param string $accessFunction
     */
    public function __construct(TwigComponentKernel $twigComponentKernel)
    {
        $this->kernel = $twigComponentKernel;
    }

    /**
     * @return array|\Twig_TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return [new SlotTokenParser(), new ComponentParser()];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'slot';
    }

    /**
     * @return array|\Twig_NodeVisitorInterface[]
     */
    public function getNodeVisitors()
    {
        return [new SlotNodeVisitor()];
    }

    public function getKernel()
    {
        return $this->kernel;
    }

}