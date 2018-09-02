<?php

namespace Olveneer\TwigComponentsBundle\Service;

use Olveneer\TwigComponentsBundle\Component\TwigComponent;
use Olveneer\TwigComponentsBundle\Component\TwigComponentInterface;
use Olveneer\TwigComponentsBundle\Component\TwigComponentMixin;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig_Node;

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
    private $mixinStore;

    /**
     * @var array
     */
    private $target;

    /**
     * ComponentRenderer constructor.
     * @param ComponentStore $componentStore
     * @param \Twig_Environment $environment
     * @param ConfigStore $configStore
     */
    public function __construct(ComponentStore $componentStore, \Twig_Environment $environment, ConfigStore $configStore)
    {
        $this->store = $componentStore;
        $this->environment = $environment;
        $this->componentDirectory = $configStore->componentDirectory;

        $this->target = ['slots' => [], 'default' => []];
        $this->mixinStore = [];
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

        /** @var string $imports */
        $importReferences = $component->importMixins();

        /** @var TwigComponentMixin $imports */
        $imports = [];

        foreach($importReferences as &$import) {
            $imports[] = $this->mixinStore[$import];
        }

        uasort($imports, function ($a, $b) {
            if (!$a instanceof TwigComponentMixin || !$b instanceof TwigComponentMixin) {
                throw new \Exception();
            }

            $a = $a->getPriority();
            $b = $b->getPriority();

            if ($a == $b) {
                return 0;
            }

            return ($a > $b) ? -1 : 1;
        });

        foreach($imports as $import) {
            $props = array_merge($props, $import->getProps());
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

        foreach($imports as $import) {
            $parameters = array_merge($parameters, $import->getParameters());
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
     * @param $mixin
     */
    public function registerMixin($mixin)
    {
        $this->mixinStore[get_class($mixin)] = $mixin;
    }

    /**
     * @param $componentName
     * @param $slots
     * @param $context
     * @throws ElementMismatchException
     * @throws MissingSlotException
     */
    public function openTarget($componentName, $slots)
    {
        $resolver = new SlotsResolver();

        $component = $this->store->get($componentName);

        $component->configureSlots($resolver);

        $slots = unserialize($slots);

        $resolver->configure($slots);

        $this->target['slots'] = $slots;
    }

    /**
     * @return void
     */
    public function closeTarget()
    {
        $this->target['slots'] = [];
        $this->target['default'] = [];
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getSlot($name)
    {
        return $this->target['slots'][$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasSlot($name)
    {
        return isset($this->target['slots'][$name]);
    }

    /**
     * @return \Twig_Environment
     */
    public function getEnv()
    {
        return $this->environment;
    }

    /**
     * @return Twig_Node
     */
    public function getDefaultNodes()
    {
        return $this->target['default'];
    }

    /**
     * @param Twig_Node $defaultNodes
     */
    public function setDefaultNodes($defaultNodes)
    {
        $this->target['default'] = $defaultNodes;
    }
}
