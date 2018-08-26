<?php

namespace Olveneer\TwigComponentsBundle\Service;

use Olveneer\TwigComponentsBundle\Component\AbstractTwigComponentInterface;
use Olveneer\TwigComponentsBundle\Component\TwigComponent;
use Olveneer\TwigComponentsBundle\Component\TwigComponentInterface;
use Olveneer\TwigComponentsBundle\Exception\OptionResolverNotPresentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TwigComponentKernel
 * @package App\Olveneer\TwigComponentsBundle\Service
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
    private $environment;

    /**
     * @var string
     */
    public $componentDirectory;

    /**
     * @var array
     */
    private $slotStore = [];

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * TwigComponentKernel constructor.
     * @param TwigComponentStore $componentStore
     * @param \Twig_Environment $environment
     * @param $componentDirectory
     */
    public function __construct(TwigComponentStore $componentStore, \Twig_Environment $environment, ConfigStore $configStore, RequestStack $requestStack)
    {
        $this->store = $componentStore;
        $this->environment = $environment;
        $this->componentDirectory = $configStore->componentDirectory;
        $this->requestStack = $requestStack;
    }

    /**
     * Returns the rendered html of a component.
     *
     * @param $name
     * @param array $props
     * @return String
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws TemplateNotFoundException
     */
    public function renderComponent($name, $props = [])
    {
        if (!($name instanceof TwigComponentInterface)) {
            $component = $this->getComponent($name, $props);


            if (!($component instanceof TwigComponentInterface)) {
               $this->throwException($name);
            }
        } else {
            $component = $name;
        }

        $parameters = $component->getParameters($props);
        $componentPath = $component->getTemplatePath();


        if (substr($componentPath, 0, 1) === '/') {
            $prefix = 'templates';
        } else {
            $prefix = 'templates/';
        }

        if (!$this->environment->getLoader()->exists($componentPath)) {
            $errorMsg = "There is no component template found for '$name'.\n Looked for the '$prefix$componentPath' template";
            throw new TemplateNotFoundException($errorMsg);
        }

        return $this->environment->render($componentPath, $parameters);
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
     * @throws TemplateNotFoundException
     */
    public function render($name, $props = [])
    {
        $component = $this->getComponent($name, $props);

        $response = $component->getRenderResponse();

        $html = $this->renderComponent($component, $props);

        $response->setContent($html);

        return $response;
    }

    public function throwException($name)
    {
        throw new \ComponentNotFoundException("No component for the name '$name' found!'");
    }

    /**
     * @param $name
     * @param array $props
     * @return null|TwigComponentInterface
     */
    public function getComponent($name, array &$props = [])
    {
        $component = $this->store->get($name);

        if (!($component instanceof TwigComponentInterface)) {
            $this->throwException($name);
        }

        $optionResolver = new OptionsResolver();

        $isUsed = $component->configureProps($optionResolver) !== false;

        if ($isUsed) {
            $props = $optionResolver->resolve($props);
        }

        $component->setProps($props);
        $component->setRequest($this->requestStack->getCurrentRequest());

        return $component;
    }

    /**
     * Initializes the component and adds it to the store
     *
     * @param TwigComponent $component
     */
    public function register(TwigComponent $component)
    {
        $component->setKernel($this);
        $component->setComponentsRoot($this->componentDirectory);

        $this->store->add($component);
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
