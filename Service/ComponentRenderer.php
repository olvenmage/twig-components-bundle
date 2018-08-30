<?php

namespace Olveneer\TwigComponentsBundle\Service;

use Olveneer\TwigComponentsBundle\Component\TwigComponent;
use Olveneer\TwigComponentsBundle\Component\TwigComponentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ComponentRenderer
 * @package Olveneer\TwigComponentsBundle\Service
 */
class ComponentRenderer
{

    /**
     * @var ComponentStore
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
     * ComponentRenderer constructor.
     * @param ComponentStore $componentStore
     * @param \Twig_Environment $environment
     * @param ConfigStore $configStore
     * @param RequestStack $requestStack
     */
    public function __construct(ComponentStore $componentStore, \Twig_Environment $environment, ConfigStore $configStore, RequestStack $requestStack)
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

    /**
     * @param $name
     */
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

        return $component;
    }

    /**
     * Initializes the component and adds it to the store
     *
     * @param TwigComponent $component
     */
    public function register(TwigComponent $component)
    {
        $component->setRenderer($this);
        $component->setComponentsRoot($this->componentDirectory);

        $this->store->add($component);
    }

    /**
     * @param $slots
     */
    public function openSlots($slots)
    {
       $this->slotStore = json_decode($slots, true);
    }

    /**
     * @return mixed
     */
    public function closeSlots()
    {
        $this->slotStore = [];
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getSlot($name)
    {
        return $this->slotStore[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasSlot($name)
    {
        return isset($this->slotStore[$name]);

    }
}
