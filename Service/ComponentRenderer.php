<?php

namespace Olveneer\TwigComponentsBundle\Service;

use Olveneer\TwigComponentsBundle\Component\TwigComponent;
use Olveneer\TwigComponentsBundle\Component\TwigComponentInterface;
use Olveneer\TwigComponentsBundle\Component\TwigComponentMixin;
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

        $this->target = ['slots' => [], 'context' => []];
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
            $name = $component->getName();
        }

        /** @var string $imports */
        $importReferences = $component->importMixins();

        /** @var TwigComponentMixin $imports */
        $imports = [];

        foreach ($importReferences as &$import) {
            if (!isset($this->mixinStore[$import])) {
                throw new \Exception("The mixin '$import' is not registered as a mixin.");
            }

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

        foreach ($imports as $import) {
            foreach ($import->getProps() as $key => $prop) {
                $props[$key] = $prop;
            }
        }

        $parameters = $component->getParameters($props);
        $componentPath = $component->getTemplatePath();

        if (!$this->environment->getLoader()->exists($componentPath)) {
            if (substr($componentPath, 0, 1) === '/') {
                $prefix = 'templates';
            } else {
                $prefix = 'templates/';
            }

            $className = get_class($component);

            $errorMsg = "There is no component template found for the '$className' component with the name '$name'.\n Looked for '$prefix$componentPath' but found nothing.";
            throw new TemplateNotFoundException($errorMsg);
        }

        foreach ($imports as $import) {
            foreach ($import->getParameters() as $key => $parameter) {
                $parameters[$key] = $parameter;
            }
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
     * @throws ElementMismatchException
     * @throws MissingSlotException
     */
    public function openTarget($componentName, $slots, $context)
    {
        $resolver = new SlotsResolver();

        $component = $this->store->get($componentName);

        if (!$component instanceof TwigComponent) {
            throw new ComponentNotFoundException("No component with the name $componentName found when using {% get %}");
        }

        $component->configureSlots($resolver);

        $slots = unserialize($slots);

        $resolver->configure($slots);

        $this->target['slots'] = $slots;
        $this->target['context'] = $context;
    }

    /**
     * @return void
     */
    public function closeTarget()
    {
        $this->target['slots'] = [];
        $this->target['context'] = [];
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

    public function getContext()
    {
        return $this->target['context'];
    }
}
