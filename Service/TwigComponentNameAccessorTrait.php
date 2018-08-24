<?php

namespace Olveneer\TwigComponentsBundle\Service;

use Olveneer\TwigComponentsBundle\Component\NamedTwigComponentInterface;

/**
 * Trait TwigComponentNameAccessorTrait
 * @package Olveneer\TwigComponentsBundle\Service
 */
trait TwigComponentNameAccessorTrait
{

    /**
     * @param TwigComponentInterface $component
     * @return string
     */
    public function getComponentName($component)
    {

        if ($component instanceof NamedTwigComponentInterface) {
            $name = $component->getName();
        } else {
            $className = get_class($component);
            $forwardSlashed = str_replace('\\', '/', $className);

            $name = lcfirst(basename($forwardSlashed));
        }

        return $name;
    }
}
