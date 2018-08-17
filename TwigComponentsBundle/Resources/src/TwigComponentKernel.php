<?php

namespace App\Olveneer\TwigComponentsBundle\Resources\src;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class TwigComponentKernel
 * @package App\Olveneer\TwigComponentsBundle\Resources\src
 */
class TwigComponentKernel
{

    /**
     * @var TwigComponentStore
     */
    private $store;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * TwigComponentKernel constructor.
     * @param TwigComponentStore $componentStore
     * @param \Twig_Environment $twig
     */
    public function __construct(TwigComponentStore $componentStore, \Twig_Environment $twig)
    {
        $this->store = $componentStore;
        $this->twig = $twig;
    }

    /**
     * Returns the rendered html of the component.
     *
     * @param $alias
     * @param array $props
     * @return String
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderComponent($alias, $props = [])
    {
        $component = $this->store->getComponent($alias);

        if (!$component instanceof TwigComponentInterface) {
            return '';
        }

        $parameters = $component->getParameters($props);

        $alias = $component->getAlias();
        $componentPath = $this->getComponentPath($alias);

        if (!$this->twig->getLoader()->exists($componentPath)) {
            $errorMsg = "There is no component template found for '$alias'.\n Looked for the '$componentPath' template";
            throw new \Twig_Error_Loader($errorMsg);
        }
        return $this->twig->render($componentPath, $parameters);
    }

    /**
     * Returns the path where the component twig template should be located.
     *
     * @param $alias
     * @return string
     */
    public function getComponentPath($alias)
    {
        return  '/components/' . $alias . '.html.twig';
    }

    /**
     * Returns a response holding the html of a component.
     *
     * @param $alias
     * @param array $props
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($alias, $props = []): Response
    {
        $response = new Response();

        $html = $this->renderComponent($alias, $props);

        $response->setContent($html);

        return $response;
    }

    /**
     * Returns the parameters of a component.
     *
     * @param $alias
     * @param array $props
     * @return array
     */
    public function getComponentParameters($alias, $props = [])
    {
       $component = $this->store->getComponent($alias);

       if (!$component instanceof TwigComponentInterface) {
           return [];
       }

       return $component->getParameters($props);
    }
}