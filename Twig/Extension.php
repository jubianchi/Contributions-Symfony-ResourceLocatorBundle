<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\Twig;

use Hoathis\Bundle\ResourceLocatorBundle\Wrapper;

class Extension extends \Twig_Extension
{
    protected $wrapper;

    function __construct(Wrapper $wrapper)
    {
        $this->wrapper = $wrapper;
    }

    public function getName()
    {
        return 'hoathis.locator.twig.extension';
    }

    public function getFunctions()
    {
        $wrapper = $this->wrapper;

        return array(
            new \Twig_SimpleFunction(
                'resolve',
                function($path) use ($wrapper) {
                    return $wrapper->resolve($path);
                }
            )
        );
    }
} 
