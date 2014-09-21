<?php

namespace Hoathis\Bundle\ResourceLocatorBundle;

use Hoa\Core;

class Wrapper extends Core\Protocol\Wrapper implements \IteratorAggregate, \ArrayAccess
{
    protected static $protocols;

    public function init()
    {
        foreach (self::$protocols as $protocol) {
            $this->register($protocol);
        }
    }

    protected function register(Protocol $protocol)
    {
        if (false === in_array($protocol->getScheme(), stream_get_wrappers())) {
            if (false === stream_wrapper_register($protocol->getScheme(), get_class($this))) {
                throw new \RuntimeException('Unable to register wrapper for protocol ' . $protocol->getScheme() . '://');
            }
        }
    }

    protected function unregister(Protocol $protocol)
    {
        if (true === in_array($protocol->getScheme(), stream_get_wrappers())) {
            if (false === stream_wrapper_unregister($protocol->getScheme())) {
                throw new \RuntimeException('Unable to unregister wrapper for protocol ' . $protocol->getScheme() . '://');
            }
        }
    }

    public function addProtocol(Protocol $protocol)
    {
        if (isset(self::$protocols[$protocol->getScheme()])) {
            if ($protocol !== self::$protocols[$protocol->getScheme()]) {
                $this->unregister(self::$protocols[$protocol->getScheme()]);
            }
        }

        $this->register(self::$protocols[$protocol->getScheme()] = $protocol);
    }

    public function getIterator()
    {
        return new \ArrayIterator(self::$protocols);
    }

    public function offsetExists($offset)
    {
        return isset(self::$protocols[$offset]);
    }

    public function offsetGet($offset)
    {
        return self::$protocols[$offset];
    }

    public function offsetSet($offset, $value)
    {
        self::$protocols[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset(self::$protocols[$offset]);
    }

    public function resolve($path, $exists = true)
    {
        return static::realPath($path, $exists);
    }

    public static function realPath($path, $exists = true)
    {
        if (preg_match('/^(\w+):\/\//', $path, $matches) > 0 && isset(self::$protocols[$matches[1]])) {
            return self::$protocols[$matches[1]]->resolve($path, $exists);
        }

        throw new \RuntimeException('Could not locate resource: ' . $path);
    }
} 
