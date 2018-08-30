<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;

/**
 * Class CollectNode
 * @package Olveneer\TwigComponentsBundle\Twig\tag\component
 */
class CollectNode extends \Twig_Node implements \Twig_NodeOutputInterface
{
    /**
     * SlotNode constructor.
     * @param $params
     * @param int $lineno
     * @param null $tag
     */
    public function __construct($params, $lineno = 0, $tag = null)
    {
        parent::__construct(['params' => $params], [], $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $params = $this->getNode('params');

        $compiler
            ->addDebugInfo($this);

        $compiler
            ->raw('$renderer = $this->extensions[')
            ->string("Olveneer\TwigComponentsBundle\Twig\SlotExtension")
            ->write(']->getRenderer();')->raw("\n");

        /** @var \Twig_Node[] $nodes */
        $nodes = $params->nodes;

        $name = $nodes[1]->getAttribute('value');
        $html = $nodes[0]->getAttribute('data');

        $compiler->write('$isSlotted = $renderer->hasSlot("' . $name. '"); ')
            ->raw("\n")
            ->write('if ($isSlotted) { $html = $renderer->getSlot("' . $name . '"); } else { $html = "' . $html . '"; } ')
            ->raw("\n ")
            ->write('echo $html;');
    }
}
