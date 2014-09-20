<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\Protocol;

class BundlesProtocol extends CustomProtocol
{
    public function __construct(array $reach)
    {
        $bundles = array();

        foreach ($reach as $bundle) {
            $reflector = new \ReflectionClass($bundle);

            $bundles[] = dirname(dirname($reflector->getFileName()));
        }

        parent::__construct('bundles', array_unique($bundles));
    }
} 
