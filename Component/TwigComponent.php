<?php

namespace Olveneer\TwigComponentsBundle\Component;

use Symfony\Component\HttpFoundation\Response;


/**
 * Class TwigComponent
 * @package Olveneer\TwigComponentsBundle\Component
 */
class TwigComponent implements ComplexTwigComponentInterface
{

    private $componentsRoot;

    /**
     * Returns the parameters to be used when rendering the template.
     * Props can be provided when rendering the component to make it more dynamic.
     *
     * @param array $props
     * @return array
     */
    public function getParameters(array $props = [])
    {
        return $props;
    }

    /**
     *  Returns a string to use as a name for the component.
     *
     * @return String
     */
    public function getName()
    {
        return lcfirst(basename(get_class($this)));
    }

    /**
     * Returns the template file name for the component.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->getName() . ".html.twig";
    }

    /**
     * Returns the entire path of the component template location.
     *
     * @return string
     */
    public function getTemplatePath()
    {
       return $this->getTemplateDirectory() . '/' . $this->getTemplateName();
    }

    public function getTemplateDirectory()
    {
        return $this->getComponentsRoot();
    }

    /**
     * Returns the base response to use when rendering the component via the renderView() method.
     *
     * @return Response
     */
    public function getRenderResponse()
    {
        return new Response();
    }

    /**
     * Returns the directory name that holds the component.
     *
     * @return string
     *
     */
    public function getComponentsRoot()
    {
        return $this->componentsRoot;
    }

    /**
     * Sets the directory name that holds the component.
     *
     * @param string $componentsRoot
     *
     * @return string
     */
    public function setComponentsRoot($componentsRoot)
    {
        $this->componentsRoot = $componentsRoot;
    }
}