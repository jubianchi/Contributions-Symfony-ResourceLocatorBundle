<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BundlesAwarePass implements CompilerPassInterface
{
    const TAG_NAME = 'hoathis.locator.bundles_aware';

    protected $bundles = array();

    public function __construct($bundles)
    {
        foreach ($bundles as $bundle) {
            $reflector = new \ReflectionClass($bundle);

            $this->bundles[] = dirname($reflector->getFileName());
        }
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (false === isset($attribute['path'])) {
                    continue;
                }

                $protocol = $container->getDefinition($id);
                $bundles = array_filter(
                    array_map(
                        function($path) use ($attribute) {
                            return $path . $attribute['path'];
                        },
                        $this->bundles
                    ),
                    function($path) {
                        return is_dir($path);
                    }
                );

                $protocol->addMethodCall('addReaches', array($bundles));
            }
        }
    }
}
