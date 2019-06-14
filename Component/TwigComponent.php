<?php

namespace Olveneer\TwigComponentsBundle\Component;

use Olveneer\TwigComponentsBundle\Exception\ComponentNotFoundException;
use Olveneer\TwigComponentsBundle\Exception\MixinNotFoundException;
use Olveneer\TwigComponentsBundle\Exception\TemplateNotFoundException;
use Olveneer\TwigComponentsBundle\Service\SlotsResolver;
use Olveneer\TwigComponentsBundle\Service\ComponentRenderer;
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
     * @var ComponentRenderer
     */
    private $renderer;

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

        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', lcfirst(basename($forwardSlashed)), $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
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

    public function configureSlots(SlotsResolver $resolver)
    {

    }

    /**
     * Injects the renderer into the component for rendering.
     *
     * @param ComponentRenderer $componentRenderer
     * @return void
     */
    public function setRenderer(ComponentRenderer $componentRenderer)
    {
        $this->renderer = $componentRenderer;
    }

    /**
     * Returns the rendered html of the component.
     *
     * @param array $props
     * @return String
     * @throws ComponentNotFoundException
     * @throws MixinNotFoundException
     * @throws TemplateNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderComponent(array $props = [])
    {
        return $this->renderer->renderComponent($this->getName(), $props);
    }

    /**
     * Returns a response holding the html of the component.
     *
     * @param array $props
     * @return Response
     * @throws ComponentNotFoundException
     * @throws MixinNotFoundException
     * @throws TemplateNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(array $props = [])
    {
        return $this->renderer->render($this->getName(), $props);
    }

    /**
     * Returns an array containing references to the desired mixins.
     *
     * @return array
     */
    public function importMixins()
    {
        return [];
    }

    /**
     * Whether or not the props should automatically be injected into the parameters.
     * The injecting of a prop only happens if it doesn't already exist in the parameters.
     *
     * @return bool
     */
    public function appendsProps()
    {
        return true;
    }
}
