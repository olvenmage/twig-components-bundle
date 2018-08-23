<?php

namespace Olveneer\TwigComponentsBundle\Resources\Service;

use Olveneer\TwigComponentsBundle\Resources\Component\NamedTwigComponentInterface;

/**
 * Trait TwigComponentNameAccessorTrait
 * @package Olveneer\TwigComponentsBundle\Resources\Service
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
