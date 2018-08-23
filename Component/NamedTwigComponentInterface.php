<?php

namespace Olveneer\TwigComponentsBundle\Component;

/**
 * Interface NamedTwigComponentInterface
 * @package App\Olveneer\TwigComponentsBundle\Service
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
