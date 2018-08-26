<?php

namespace Olveneer\TwigComponentsBundle\Component;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Olveneer\TwigComponentsBundle\Service\TwigComponentKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface TwigComponentInterface
 * @package Olveneer\TwigComponentsBundle\Service
 */
interface TwigComponentInterface
{
    /**
     * Returns the parameters to be used when rendering the template.
     * Props can be provided when rendering the component to make it more dynamic.
     *
     * @param array $props
     * @return array
     */
    public function getParameters(array $props = []);

    /**
     *  Returns a string to use as a name for the component.
     *
     * @return String
     */
    public function getName();

    /**
     * Configures the props using the Symfony OptionResolver
     *
     * @param OptionsResolver $resolver
     * @return void|bool
     */
    public function configureProps(OptionsResolver $resolver);

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
     * Returns the base response to use when rendering the component via the render() method.
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

    /**
     * Injects the kernel into the component for rendering.
     *
     * @param TwigComponentKernel $twigComponentKernel
     * @return void
     */
    public function setKernel(TwigComponentKernel $twigComponentKernel);

    /**
     * Returns a response holding the html of a component.
     *
     * @param array $props
     * @return string
     */
    public function render(array $props = []);

    /**
     * Returns the rendered html of the component.
     *
     * @param array $props
     * @return string
     */
    public function renderComponent(array $props = []);


    /**
     * Injects the current request into the component
     *
     * @param $request
     * @return mixed
     */
    public function setRequest($request);

    /**
     * Get the current active request
     *
     * @return Request
     */
    public function getRequest();
}