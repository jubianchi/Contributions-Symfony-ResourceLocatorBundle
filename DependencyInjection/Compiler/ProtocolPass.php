<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProtocolPass implements CompilerPassInterface
{
    const TAG_NAME = 'hoathis.locator.protocol';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('hoathis.locator.wrapper')) {
            return;
        }

        $wrapper = $container->getDefinition('hoathis.locator.wrapper');

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $attributes) {
            $wrapper->addMethodCall('addProtocol', array(new Reference($id)));
        }
    }
}
