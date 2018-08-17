<?php

namespace App\Olveneer\TwigComponentsBundle\Resources\src;

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
    public function getParameters(array $props): array;

    /**
     * Returns the alias of the component for later referencing.
     *
     * @return String
     */
    public function getAlias(): String;
}