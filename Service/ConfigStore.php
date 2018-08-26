<?php

namespace Olveneer\TwigComponentsBundle\Service;

/**
 * Holds the configurations to be injected
 *
 * Class ConfigStore
 * @package Olveneer\TwigComponentsBundle\Service
 */
class ConfigStore
{

    /**
     * @var string
     */
    public $componentDirectory;

    /**
     * @var string
     */
    public $renderFunction;

    /**
     * @var string
     */
    public $accessFunction;

    /**
     * ConfigStore constructor.
     * @param $componentDirectory
     * @param $renderFunction
     * @param $accessFunction
     */
    public function __construct(
        $componentDirectory,
        $renderFunction,
        $accessFunction
    )
    {
        $this->componentDirectory = $componentDirectory;
        $this->renderFunction = $renderFunction;
        $this->accessFunction = $accessFunction;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return get_object_vars($this);
    }
}