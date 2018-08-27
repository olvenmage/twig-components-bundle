<?php

namespace Olveneer\TwigComponentsBundle\Twig\tags\slot;

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
    
    private $slots = [];

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
            $node->setAttribute('counter', $this->counter--);
        }
        return $node;
    }

    public function getPriority()
    {
        return 0;
    }

    public function getSlots()
    {
        return $this->slots;
    }
}
