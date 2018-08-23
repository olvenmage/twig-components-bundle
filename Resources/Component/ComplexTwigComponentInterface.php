<?php

namespace Olveneer\TwigComponentsBundle\Resources\Component;

use Symfony\Component\HttpFoundation\Response;

interface ComplexTwigComponentInterface extends  NamedTwigComponentInterface
{

    /**
     * Returns the template file name for the component.
     *
     * @return string;
     */
    public function getComponentFileName();

    /**
     * Returns the entire path of the component template location.
     *
     * @return mixed
     */
    public function getComponentFilePath();

    /**
     * Returns the base response to use when rendering the component via the renderView() method.
     *
     * @return Response
     */
    public function getRenderResponse();

}