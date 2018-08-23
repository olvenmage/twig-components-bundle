<?php

namespace Olveneer\TwigComponentsBundle\Resources\Component;
use Olveneer\TwigComponentsBundle\Resources\Service\TwigComponentKernel;
use Olveneer\TwigComponentsBundle\Resources\Service\TwigComponentNameAccessorTrait;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class TwigComponent
 * @package Olveneer\TwigComponentsBundle\Resources\Component
 */
class TwigComponent implements ComplexTwigComponentInterface
{
    use TwigComponentNameAccessorTrait;

    /**
     * @var TwigComponentKernel
     */
    private $kernel;

    /**
     * TwigComponent constructor.
     * @param TwigComponentKernel $twigComponentKernel
     */
    public function __construct(TwigComponentKernel $twigComponentKernel)
    {
        $this->kernel = $twigComponentKernel;
    }

    /**
     * Returns the parameters to be used when rendering the template.
     * Props can be provided when rendering the component to make it more dynamic.
     *
     * @param array $props
     * @return array
     */
    public function getParameters(array $props= [])
    {
        return $props;
    }

    /**
     *  Returns a string to use as a name for the component.
     *
     * @return String
     */
    public function getName()
    {
        return lcfirst(basename(get_class($this)));
    }

    /**
     * Returns the template file name for the component.
     *
     * @return string;
     */
    public function getComponentFileName()
    {
        return $this->getName() . ".html.twig";
    }

    /**
     * Returns the entire path of the component template location.
     *
     * @return mixed
     */
    public function getComponentFilePath()
    {
        return $this->kernel->getComponentDirectory() . '/' .  $this->getComponentFileName();
    }


    /**
     * Returns the base response to use when rendering the component via the renderView() method.
     *
     * @return Response
     */
    public function getRenderResponse()
    {
        return new Response();
    }
}