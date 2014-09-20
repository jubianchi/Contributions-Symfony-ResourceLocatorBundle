<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\Protocol;


class ViewsProtocol extends CustomProtocol
{
    public function __construct($reach)
    {
        parent::__construct('views', $reach);
    }
} 
