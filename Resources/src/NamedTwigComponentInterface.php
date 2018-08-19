<?php

namespace Olveneer\TwigComponentsBundle\Resources\src;

/**
 * Interface NamedTwigComponentInterface
 * @package App\Olveneer\TwigComponentsBundle\Resources\src
 */
interface NamedTwigComponentInterface extends TwigComponentInterface
{

    /**
     *  Returns a string to use as a name for the component.
     *
     * @return String
     */
    public function getName();
}
