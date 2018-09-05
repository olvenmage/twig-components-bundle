<?php

namespace Olveneer\TwigComponentsBundle\Twig\tag;

use Olveneer\TwigComponentsBundle\Exception\GetSyntaxException;

/**
 * Class SlotNode
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class ComponentNode extends \Twig_Node implements \Twig_NodeOutputInterface
{
    /**
     * @var array
     */
    private $slotted;

    /**
     * ComponentNode constructor.
     * @param \Twig_Node_Expression $expr
     * @param \Twig_Node_Expression|null $variables
     * @param $lineno
     * @param $slotted
     * @param null $tag
     */
    public function __construct(\Twig_Node_Expression $expr, ?\Twig_Node_Expression $variables, $lineno, $slotted = [], $tag = null)
    {
        $nodes = array('expr' => $expr);

        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        $this->slotted = $slotted;

        parent::__construct($nodes, [], $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     * @throws GetSyntaxException
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $exprNode = $this->getNode('expr');

        if (!$exprNode instanceof \Twig_Node_Expression_Name) {
            throw new GetSyntaxException("Use unquoted strings for the {% get %} tag.");
        }

        $componentName = $exprNode->getAttribute('name');

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
            ->write(']->getRenderer();')->raw(PHP_EOL);

        $compiler
            ->write('$renderer->openTarget(')
            ->string($componentName)
            ->raw(',')
            ->string(serialize($this->slotted))
            ->raw(', $context')
            ->raw(');')
            ->raw(PHP_EOL);

        $compiler
            ->raw('echo ')
            ->write('$renderer->renderComponent(')
            ->string($componentName)
            ->raw(', $props')
            ->raw("); ")
            ->raw(PHP_EOL);

        $compiler
            ->write('$renderer->closeTarget(); ')->raw(PHP_EOL);
    }
}
