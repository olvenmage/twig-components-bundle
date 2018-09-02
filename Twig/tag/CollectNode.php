<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;
use Olveneer\TwigComponentsBundle\Service\ComponentRenderer;
use Olveneer\TwigComponentsBundle\Twig\SlotExtension;

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

        $compiler->write('$extension = $this->extensions[')
            ->string("Olveneer\TwigComponentsBundle\Twig\SlotExtension")
            ->raw('];')->raw(PHP_EOL);

        $compiler
            ->write('$renderer = $extension->getRenderer(); ')->raw(PHP_EOL)
            ->write('$compiler = $extension->createCompiler(); ')->raw(PHP_EOL);


        /** @var \Twig_Node[] $nodes */
        $nodes = $params->nodes;

        $name = $nodes[1]->getAttribute('value');

        $compiler->write('$renderer->setDefaultNodes(unserialize(\''. serialize($nodes[0]) .'\'));')->raw(PHP_EOL);

        $compiler->write('$exposed = [];')->raw(PHP_EOL);

        if (isset($nodes[2])) {
            $exposes = $nodes[2]->getAttribute('name');
            if ($exposes === 'exposes') {
                if (isset($nodes[3]) && $nodes[3] instanceof  \Twig_Node_Expression_Array) {
                    $compiler
                        ->write('$exposed = ')
                        ->subcompile($nodes[3])->raw(';')->raw(PHP_EOL);
                }
            }
        }

        $compiler->write('$oldContext = $context; ')->raw(PHP_EOL)
            ->write('$context = array_merge($context, $exposed);')->raw(PHP_EOL);

        $compiler
            ->write('$isSlotted = $renderer->hasSlot(')
            ->string($name)
            ->raw(');')
            ->raw(PHP_EOL);

        $compiler
            ->write('if ($isSlotted) {')->raw(PHP_EOL)
            ->indent()
                ->write('$nodes = $renderer->getSlot(')
                ->string($name)
                ->raw(');')->raw(PHP_EOL)
            ->outdent()
            ->write('} else {')->raw(PHP_EOL)
            ->indent()
                ->write('$nodes = $renderer->getDefaultNodes();')->raw(PHP_EOL)
            ->outdent()
            ->raw('}')->raw(PHP_EOL)
            ->write('$nodes->compile($compiler);')->raw(PHP_EOL)
            ->write('eval($compiler->getSource());')->raw(PHP_EOL)
            ->write('$context = $oldContext;')->raw(PHP_EOL);
    }
}
