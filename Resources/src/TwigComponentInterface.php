<?php

namespace Olveneer\TwigComponentsBundle\Resources\src;

/**
 * Interface TwigComponentInterface
 * @package App\Olveneer\TwigComponentsBundle\Resources\src
 */
interface TwigComponentInterface
{
    
    /**
     * Returns the parameters to be used when rendering the template.
     * Props can be provided when rendering the component to make it more dynamic.
     *
     * @param array $props
     * @return array
     */
    public function getParameters(array $props);

    /**
     *  Returns a string to use as a name for the component.
     *
     *  If you return nothing, a camelcase version of the class name will be used.
     *
     * @return String|void
     */
    public function getName();
}
