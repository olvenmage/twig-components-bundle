<?php

namespace Olveneer\TwigComponentsBundle\Slot;

/**
 * Class SlotNode
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class SlotNode extends \Twig_Node
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
        $count = count($this->getNode('params'));

        $compiler
            ->addDebugInfo($this);

        for ($i = 0; ($i < $count); $i++)
        {
            // argument is not an expression (such as, a \Twig_Node_Textbody)
            // we should trick with output buffering to get a valid argument to pass
            // to the functionToCall() function.
            if (!($this->getNode('params')->getNode($i) instanceof \Twig_Node_Expression))
            {



                $compiler
                    ->write('ob_start();')
                    ->raw(PHP_EOL);

                $compiler
                    ->subcompile($this->getNode('params')->getNode($i));

                $compiler
                    ->write(sprintf('$_slot[%d][] = ob_get_clean();', $this->getAttribute('counter')))
                    ->raw(PHP_EOL);
            }
            else
            {
                $compiler
                    ->write(sprintf('$_slot[%d][] = ', $this->getAttribute('counter')))
                    ->subcompile($this->getNode('params')->getNode($i))
                    ->raw(';')
                    ->raw(PHP_EOL);
            }
        }

//        $compiler
//            ->write('call_user_func_array(')
//            ->string('functionToCall')
//            ->raw(sprintf(', $_slot[%d]);', $this->getAttribute('counter')))
//            ->raw(PHP_EOL);

        $compiler
            ->write(sprintf('unset($_slot[%d]);', $this->getAttribute('counter')))
            ->raw(PHP_EOL);
    }
    
}