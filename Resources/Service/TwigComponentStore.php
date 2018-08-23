<?php

namespace Olveneer\TwigComponentsBundle\Resources\Service;

use Olveneer\TwigComponentsBundle\Resources\Component\NamedTwigComponentInterface;

/**
 * Class TwigComponentStore
 * @package App\Olveneer\TwigComponentsBundle\Resources\Service
 */
class TwigComponentStore
{
    use TwigComponentNameAccessorTrait;

    /**
     * @var TwigComponentInterface[]
     */
    private $components = [];

    /**
     * Adds a component to the store
     *
     * @param TwigComponentInterface|null $component
     */
    public function register($component)
    {
        if (!$component instanceof TwigComponentInterface && $component instanceof NamedTwigComponentInterface) {
            return;
        }

        $name = $this->getComponentName($component);

        $this->components[$name] = $component;
    }

    /**
     * Returns a component by name or class name.
     *
     * @param $name
     * @return TwigComponentInterface|null
     */
    public function get($name)
    {
        if (class_exists($name)) {
            return new $name();
        }

        if (!$this->has($name)) {
            return null;
        }

        return $this->components[$name];
    }

    /**
     * Checks if the name is registered as a component
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->components[$name]);
    }

    /**
     * Returns a list containing the names of all the registered components.
     *
     * @return array
     */
    public function getRegisteredNames()
    {
        return array_keys($this->components);
    }
}
