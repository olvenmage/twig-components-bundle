<?php

namespace Olveneer\TwigComponentsBundle\Twig\tags\component;

/**
 * Class SlotNode
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class ComponentNode extends \Twig_Node implements \Twig_NodeOutputInterface
{

    public function __construct(\Twig_Node_Expression $expr, \Twig_Node_Expression $variables = null, $only = false, $ignoreMissing = false, $lineno, $tag = null)
    {
        $nodes = array('expr' => $expr);
        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        parent::__construct($nodes, array('only' => (bool)$only, 'ignore_missing' => (bool)$ignoreMissing), $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        if ($this->getAttribute('ignore_missing')) {
            $compiler
                ->write("try {\n")
                ->indent();
        }

        $this->addGetTemplate($compiler);

        if ($this->getAttribute('ignore_missing')) {
            $compiler
                ->outdent()
                ->write("} catch (Twig_Error_Loader \$e) {\n")
                ->indent()
                ->write("// ignore missing template\n")
                ->outdent()
                ->write("}\n\n");
        }

    }

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
            ->raw('echo $this->extensions[')
            ->string("Olveneer\TwigComponentsBundle\Twig\SlotExtension")
            ->write(']->getKernel()->renderComponent(')
            ->string($componentName)
            ->write(', $props')
            ->raw("); \n");
    }


}