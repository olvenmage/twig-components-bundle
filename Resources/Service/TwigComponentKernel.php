<?php

namespace Olveneer\TwigComponentsBundle\Resources\Service;

use Olveneer\TwigComponentsBundle\Resources\Component\ComplexTwigComponentInterface;
use Olveneer\TwigComponentsBundle\Resources\Component\TwigComponentInterface;
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

    public function getComponentDirectory()
    {
        return $this->componentDirectory;
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
        $component = $this->store->get($name);

        $filePath = $this->componentDirectory . '/' . $name. '.html.twig';

        if ($component instanceof ComplexTwigComponentInterface) {
            $filePath =  $component->getComponentFilePath();
        }

        return $filePath;
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
        $component = $this->store->get($name);

        $response = new Response();

        if ($component instanceof ComplexTwigComponentInterface) {
            $response = $component->getRenderResponse();
        }

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
