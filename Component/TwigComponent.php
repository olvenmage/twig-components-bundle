<?php

namespace Olveneer\TwigComponentsBundle\Component;

use Olveneer\TwigComponentsBundle\Service\TwigComponentKernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TwigComponent
 * @package Olveneer\TwigComponentsBundle\Component
 */
class TwigComponent implements TwigComponentInterface
{

    /**
     * @var string
     */
    private $componentsRoot;

    /**
     * @var array
     */
    private $props;

    /**
     * @var TwigComponentKernel
     */
    private $kernel;

    /**
     * @var Request
     */
    private $request;

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
        $className = get_class($this);
        $forwardSlashed = str_replace('\\', '/', $className);
        
        return lcfirst(basename($forwardSlashed));
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

    /**
     * Returns the directory the template file is located in
     *
     * @return string
     */
    public function getTemplateDirectory()
    {
        return $this->getComponentsRoot();
    }

    /**
     * Returns the base response to use when rendering the component via the render() method.
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

    /**
     * Returns the props passed to the component
     *
     * @return array
     */
    public function getProps()
    {
        return $this->props;
    }

    /**
     * Sets the props passed to the component
     *
     * @param $props
     */
    public function setProps($props)
    {
        $this->props = $props;
    }

    /**
     * Configures the props using the Symfony OptionResolver
     *
     * @param OptionsResolver $resolver
     * @return void|bool
     */
    public function configureProps(OptionsResolver $resolver)
    {
        return false;
    }

    /**
     * Injects the kernel into the component for rendering.
     *
     * @param TwigComponentKernel $twigComponentKernel
     * @return void
     */
    public function setKernel(TwigComponentKernel $twigComponentKernel)
    {
        $this->kernel = $twigComponentKernel;
    }

    /**
     * Returns the rendered html of the component.
     *
     * @param array $props
     * @return String
     * @throws \Olveneer\TwigComponentsBundle\Service\TemplateNotFoundException
     */
    public function renderComponent(array $props = [])
    {
        return $this->kernel->renderComponent($this->getName(), $props);
    }

    /**
     * Returns a response holding the html of the component.
     *
     * @param array $props
     * @return Response
     * @throws \Olveneer\TwigComponentsBundle\Service\TemplateNotFoundException
     */
    public function render(array $props = [])
    {
        return $this->kernel->render($this->getName(), $props);
    }

    /**
     *  Injects the current request into the component
     *
     * @param $request
     * @return mixed|void
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Get the current active request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
