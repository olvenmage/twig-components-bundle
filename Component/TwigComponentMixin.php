<?php

namespace Olveneer\TwigComponentsBundle\Component;

/***
 * Class TwigComponentMixin
 * @package Olveneer\TwigComponentsBundle\Component
 *
 * A mixin is called when a component is rendered and alters the props and parameters.
 */
class TwigComponentMixin
{

    /**
     * @return array
     *
     * Merges with the parameters.
     */
    public function getParameters()
    {
        return [];
    }

    /**
     * @return array
     *
     * Merges with the props.
     */
    public function getProps()
    {
        return [];
    }

    /**
     * @return int
     *
     * The execution order of all the mixins. Mixins with the same key override the earlier ones.
     * Lower goes first.
     */
    public function getPriority()
    {
        return 0;
    }

}