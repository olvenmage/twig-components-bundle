<?php

namespace Olveneer\TwigComponentsBundle\Component;

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
     * @param string $componentDirectory
     * @return mixed
     */
    public function getComponentFilePath();

    /**
     * Returns the directory the template file is located in
     *
     * @return mixed
     */
    public function getComponentFileDirectory();

    /**
     * Returns the base response to use when rendering the component via the renderView() method.
     *
     * @return Response
     */
    public function getRenderResponse();

    /**
     * Returns the directory name that holds the component.
     *
     * @return string
     *
     */
    public function getComponentsRoot();

    /**
     * Sets the directory name that holds the component.
     *
     * @param string $componentsRoot
     *
     * @return string
     */
    public function setComponentsRoot($componentsRoot);

}