<?php

namespace Olveneer\TwigComponentsBundle\Resources\Component;

/**
 * Interface TwigComponentInterface
 * @package Olveneer\TwigComponentsBundle\Resources\Service
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
    public function getParameters(array $props = []);
}