<?php

namespace Olveneer\TwigComponentsBundle\Resources\Service;

/**
 * Interface NamedTwigComponentInterface
 * @package App\Olveneer\TwigComponentsBundle\Resources\Service
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
