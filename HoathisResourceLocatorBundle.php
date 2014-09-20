<?php

namespace Hoathis\Bundle\ResourceLocatorBundle;

use Hoathis\Bundle\ResourceLocatorBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HoathisResourceLocatorBundle extends Bundle
{
    public function getContainer()
    {
        return $this->container;
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $bundles = $container->getParameterBag()->resolveValue($container->getParameter('kernel.bundles'));

        $container
            ->addCompilerPass(new Compiler\BundlesAwarePass($bundles))
            ->addCompilerPass(new Compiler\ProtocolPass($bundles))
        ;
    }

    public function boot()
    {
        parent::boot();

        $this->getContainer()->get('hoathis.locator.wrapper')->init();
    }
}
