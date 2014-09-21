<?php

namespace Hoathis\Bundle\ResourceLocatorBundle;

use Hoa\Core;
use Hoathis\Bundle\ResourceLocatorBundle\DataCollector\LocatorCollector;

class DebugWrapper extends Wrapper
{
    protected static $collector;

    public function __construct(LocatorCollector $collector = null)
    {
        if (null !== $collector) {
            self::$collector = $collector;
        }
    }

    public static function realPath($path, $exists = true)
    {
        $time = microtime(true);

        try {
            $resolved = parent::realPath($path, $exists);

            if (null !== self::$collector) {
                self::$collector->addResource($path, $resolved, microtime(true) - $time);
            }

            return $resolved;
        } catch (\RuntimeException $exception) {
            if (null !== self::$collector) {
                self::$collector->addResource($path, false, microtime(true) - $time);
            }

            throw $exception;
        }
    }
} 
