<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\Twig;

use Hoa\Core\Protocol\Wrapper;

class Loader extends \Twig_Loader_Filesystem
{
    protected $wrapper;

    public function __construct(Wrapper $wrapper)
    {
        $this->wrapper = $wrapper;
    }

    protected function normalizeName($name)
    {
        return preg_replace('#(?<!:)/{2,}#', '/', strtr((string) $name, '\\', '/'));
    }

    protected function findTemplate($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        try {
            $this->cache[$name] = $this->wrapper->resolve($name);
        } catch (\RuntimeException $exception) {
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s".', $name), $exception->getLine(), $exception->getFile(), $exception);
        }

        return $this->cache[$name];
    }
} 
