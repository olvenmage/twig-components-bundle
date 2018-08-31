<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;

/**
 * Class SlotNode
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class ComponentNode extends \Twig_Node implements \Twig_NodeOutputInterface
{

    /**
     * @var array
     */
    private $inserted;

    /**
     * ComponentNode constructor.
     * @param \Twig_Node_Expression $expr
     * @param \Twig_Node_Expression|null $variables
     * @param $lineno
     * @param $inserted
     * @param null $tag
     */
    public function __construct(\Twig_Node_Expression $expr, \Twig_Node_Expression $variables = null, $lineno, $inserted, $tag = null)
    {
        $nodes = array('expr' => $expr);
        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        $this->inserted = $inserted;

        parent::__construct($nodes, [], $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $this->addGetTemplate($compiler);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    protected function addGetTemplate(\Twig_Compiler $compiler)
    {
        $componentName = $this->getNode('expr')->getAttribute('value');

        $compiler->write('$props = ');

        if ($this->hasNode('variables')) {
            $compiler->subcompile($this->getNode('variables'));
        } else {
            $compiler->raw('[]');
        }

        $compiler->write(';')
            ->raw(PHP_EOL);;

        $compiler
            ->raw('$renderer = $this->extensions[')
            ->string("Olveneer\TwigComponentsBundle\Twig\SlotExtension")
            ->write(']->getRenderer();')->raw("\n");


        $compiler
            ->write('$renderer->openSlots("'.$componentName.'", \''  .  json_encode($this->inserted) . '\'); ')->raw("\n");

        $compiler
            ->raw('echo ')
            ->write('$renderer->renderComponent(')
            ->string($componentName)
            ->write(', $props')
            ->raw("); ")->raw("\n");

        $compiler
            ->write('$renderer->closeSlots(); ')->raw("\n");
    }
}
