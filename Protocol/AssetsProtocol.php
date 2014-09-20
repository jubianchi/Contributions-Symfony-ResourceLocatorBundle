<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\Protocol;


use Symfony\Component\Filesystem\Filesystem;

class AssetsProtocol extends CustomProtocol
{
    protected $webroot;

    public function __construct($reach, $webroot)
    {
        $reach = array_merge(
            array($reach),
            array_filter(
                glob($reach . '/*'),
                function($path) {
                    return is_dir($path);
                }
            )
        );

        $this->webroot = realpath($webroot);

        parent::__construct('assets', $reach);
    }

    public function resolve($path, $exists = true, $unfold = false)
    {
        $fs = new Filesystem();
        $resolved = parent::resolve($path, $exists, $unfold);

        return '/' . $fs->makePathRelative(dirname($resolved), $this->webroot) . basename($resolved);
    }
} 
