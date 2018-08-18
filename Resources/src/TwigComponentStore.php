<?php

namespace Olveneer\TwigComponentsBundle\Resources\src;

/**
 * Class TwigComponentStore
 * @package App\Olveneer\TwigComponentsBundle\Resources\src
 */
class TwigComponentStore
{
    /**
     * @var TwigComponentInterface[]
     */
    private $components = [];
    
    /**
     * Adds a component to the store
     *
     * @param TwigComponentInterface|null $component
     */
    public function addComponent($component)
    {
        if (!$component instanceof TwigComponentInterface) {
            return;
        }

        $this->components[$component->getAlias()] = $component;
    }

    /**
     * Retrieves a component out of the store using it's alias
     *
     * @param $alias
     * @return TwigComponentInterface|null
     */
    public function getComponent($alias): ?TwigComponentInterface
    {
        if (!isset($this->components[$alias])) {
            return null;
        }

        return $this->components[$alias];
    }
}
