<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HoathisResourceLocatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('services_debug.yml');

            $container->setDefinition('hoathis.locator.wrapper', $container->findDefinition('hoathis.locator.wrapper.debug'));
        }
    }
}
