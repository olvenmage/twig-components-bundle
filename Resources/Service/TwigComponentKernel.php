<?php

namespace Olveneer\TwigComponentsBundle\Resources\Service;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class TwigComponentKernel
 * @package App\Olveneer\TwigComponentsBundle\Resources\Service
 */
class TwigComponentKernel
{
    
    use TwigComponentNameAccessorTrait;

    /**
     * @var TwigComponentStore
     */
    private $store;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $componentDirectory;

    /**
     * TwigComponentKernel constructor.
     * @param TwigComponentStore $componentStore
     * @param \Twig_Environment $twig
     * @param $componentDirectory
     */
    public function __construct(TwigComponentStore $componentStore, \Twig_Environment $twig, $componentDirectory)
    {
        $this->store = $componentStore;
        $this->twig = $twig;
        $this->componentDirectory = $componentDirectory;
    }

    /**
     * Returns the rendered html of the component.
     *
     * @param $name
     * @param array $props
     * @return String
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderComponent($name, $props = [])
    {
        $component = $this->store->get($name);
        
        if (!$component instanceof TwigComponentInterface) {
            return '';
        }

        $parameters = $component->getParameters($props);

        $name = $this->getComponentName($component);
        $componentPath = $this->getComponentPath($name);

        if (!$this->twig->getLoader()->exists($componentPath)) {
            $errorMsg = "There is no component template found for '$name'.\n Looked for the '$componentPath' template";
            throw new \Twig_Error_Loader($errorMsg);
        }
        return $this->twig->render($componentPath, $parameters);
    }

    /**
     * Returns the path where the component twig template should be located.
     *
     * @param $name
     * @return string
     */
    public function getComponentPath($name)
    {
        return $this->componentDirectory . '/' . $name . '.html.twig';
    }

    /**
     * Returns a response holding the html of a component.
     *
     * @param $name
     * @param array $props
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderView($name, $props = [])
    {
        $response = new Response();

        $html = $this->renderComponent($name, $props);

        $response->setContent($html);

        return $response;
    }

    /**
     * Returns the parameters of a component.
     *
     * @param $name
     * @param array $props
     * @return array
     */
    public function getComponentParameters($name, $props = [])
    {
       $component = $this->store->get($name);

       if (!$component instanceof TwigComponentInterface) {
           return [];
       }

       return $component->getParameters($props);
    }
}
