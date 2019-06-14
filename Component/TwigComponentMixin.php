<?php

namespace Olveneer\TwigComponentsBundle\Component;

use Symfony\Component\OptionsResolver\OptionsResolver;

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
    public function getParameters(array $props = [])
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

    /**
     * Configures the props using the Symfony OptionResolver
     *
     * @param OptionsResolver $resolver
     * @return void|bool
     */
    public function configureProps(OptionsResolver $resolver)
    {
        return false;
    }
}
