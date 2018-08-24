<?php

namespace Olveneer\TwigComponentsBundle\Service;

use App\Component\OptionResolverComponent;
use Olveneer\TwigComponentsBundle\Component\AbstractTwigComponentInterface;
use Olveneer\TwigComponentsBundle\Component\TwigComponentInterface;
use Olveneer\TwigComponentsBundle\Exception\OptionResolverNotPresentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TwigComponentKernel
 * @package App\Olveneer\TwigComponentsBundle\Service
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
    public $componentDirectory;

    /**
     * @var array
     */
    private $slotStore = [];

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
        $component = $this->getComponent($name, $props);

        if (!$component instanceof TwigComponentInterface) {
            return '';
        }



        $parameters = $component->getParameters($props);

        $name = $this->getComponentName($component);
        $componentPath = $this->getComponentPath($name);


        if (substr($componentPath, 0, 1) === '/') {
            $prefix = 'templates';
        } else {
            $prefix = 'templates/';
        }

        if (!$this->twig->getLoader()->exists($componentPath)) {
            $errorMsg = "There is no component template found for '$name'.\n Looked for the '$prefix$componentPath' template";
            throw new \Twig_Error_Loader($errorMsg);
        }

        $this->currentTemplate = $componentPath;
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
        $component = $this->getComponent($name);

        $filePath = $this->componentDirectory . '/' . $name . '.html.twig';

        if ($component instanceof AbstractTwigComponentInterface) {
            $filePath = $component->getTemplatePath();
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
    public function render($name, $props = [])
    {
        $component = $this->getComponent($name, $props);

        $response = new Response();

        if ($component instanceof AbstractTwigComponentInterface) {
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
        $component = $this->getComponent($name);

        if (!$component instanceof TwigComponentInterface) {
            return [];
        }

        return $component->getParameters($props);
    }

    /**
     * @param $name
     * @param null $props
     * @return null|TwigComponentInterface
     */
    public function getComponent($name, &$props = null)
    {
        $component = $this->store->get($name);

        if ($component instanceof AbstractTwigComponentInterface) {
            if ($component->getComponentsRoot() === null) {
                $component->setComponentsRoot($this->componentDirectory);
            }

            if ($props) {
                $optionResolver = new OptionsResolver();

                $isUsed = $component->configureProps($optionResolver) !== false;

                if ($isUsed) {
                    $props = $optionResolver->resolve($props);
                }
                
                $component->setProps($props);
            }

        }

        return $component;
    }

    /**
     * [WIP]
     *
     * @param $html
     * @param $template
     * @param $name
     */
    public function registerSlot($html, $template, $name)
    {
        $this->slotStore[$name] = $html;
    }

    /**
     * [WIP]
     *
     * @param $name
     * @return mixed
     */
    public function getSlot($name)
    {
        return $this->slotStore[$name];
    }
}
