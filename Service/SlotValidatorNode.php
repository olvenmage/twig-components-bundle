<?php

namespace Olveneer\TwigComponentsBundle\Service;

/**
 * Class SlotValidatorNode
 * @package Olveneer\TwigComponentsBundle\Service
 */
class SlotValidatorNode
{
    /**
     * @var SlotsResolver
     */
    private $resolver;

    /**
     * @var
     */
    private $slot;

    /**
     * SlotValidator constructor.
     * @param $slot
     * @param $resolver
     */
    public function __construct($slot, $resolver)
    {
        $this->slot = $slot;
        $this->resolver = $resolver;
    }

    /**
     * @param $tag
     * @param int $amount
     * @param array $attributes
     * @return SlotsResolver
     */
    public function requiresElement($tag, $amount = 1, $attributes = [])
    {
       $this->resolver->slots[$this->slot]['requiredElements'][$tag] = ['attributes' => $attributes, 'amount' => $amount];

       return $this->resolver;
    }
}
