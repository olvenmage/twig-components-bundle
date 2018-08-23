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
    public function getTemplateName();

    /**
     * Returns the entire path of the component template location.
     *
     * @return string
     */
    public function getTemplatePath();

    /**
     * Returns the directory the template file is located in
     *
     * @return string
     */
    public function getTemplateDirectory();

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

    /**
     * Returns the props passed to the component
     *
     * @return array
     */
    public function getProps();

    /**
     * Sets the props passed to the component
     *
     * @param $props
     */
    public function setProps($props);

}