<?php

namespace Olveneer\TwigComponentsBundle\Resources\src;

/**
 * Trait TwigComponentNameAccessorTrait
 * @package Olveneer\TwigComponentsBundle\Resources\src
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

            // get only the last part of the class path.
            $className = basename(get_class($component));

            $name = lcfirst($className);
        }

        return $name;
    }
}
