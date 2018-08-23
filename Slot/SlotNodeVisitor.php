<?php

namespace Olveneer\TwigComponentsBundle\Slot;

use Olveneer\TwigComponentsBundle\Service\TwigComponentKernel;

/**
 * Class SlotNodeVisitor
 * @package Olveneer\TwigComponentsBundle\Slot
 */
class SlotNodeVisitor implements \Twig_NodeVisitorInterface
{
    /**
     * @var int 
     */
    private $counter = 0;
    
    /**
     * @var TwigComponentKernel 
     */
    private $kernel;
    
    public function __construct(TwigComponentKernel $twigComponentKernel)
    {
        $this->kernel = $twigComponentKernel;
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return \Twig_Node
     */
    public function enterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if ($node instanceof SlotNode) {
            $node->setAttribute('counter', $this->counter++);
        }
        return $node;
    }

    /**
     * @param \Twig_Node $node
     * @param \Twig_Environment $env
     * @return false|\Twig_Node
     */
    public function leaveNode(\Twig_Node $node, \Twig_Environment $env)
    {

        if ($node instanceof SlotNode) {

            $count = count($node->getNode('params'));

            $html = "";

            $slotname = null;

            for ($i = 0; ($i < $count); $i++) {
                // argument is not an expression (such as, a \Twig_Node_Textbody)
                // we should trick with output buffering to get a valid argument to pass
                // to the functionToCall() function.
                $paramNode = $node->getNode('params')->getNode($i);

                if ($paramNode instanceof \Twig_Node_Expression_Constant) {
                    if ($paramNode->hasAttribute('value')) {
                        $slotName = $paramNode->getAttribute('value');
                    }
                } else if (!($paramNode instanceof \Twig_Node_Expression)) {
                    $html .= $paramNode->getAttribute('data');
                }
            }

            $node->setAttribute('counter', $this->counter--);
            $templateName = $node->getTemplateName();
            
                $this->kernel->registerSlot($html, $templateName, $slotName);
        }
        return $node;
    }

//    public function leaveNode(\Twig_Node $node, \Twig_Environment $env)
//    {
//
//        if ($node instanceof SlotNode) {
//            $node->setAttribute('counter', $this->counter--);
//        }
//
//
//        return $node;
//    }

    public function getPriority()
    {
        return 0;
    }
}
