<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\Protocol;

use Hoathis\Bundle\ResourceLocatorBundle;

class CustomProtocol extends ResourceLocatorBundle\Protocol
{
    public function __construct($scheme, $reach)
    {
        $scheme = str_replace('://', '', $scheme) . '://';

        parent::__construct($scheme, $reach);
    }
} 
