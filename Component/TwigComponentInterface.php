<?php

namespace Olveneer\TwigComponentsBundle\Component;

/**
 * Interface TwigComponentInterface
 * @package Olveneer\TwigComponentsBundle\Service
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