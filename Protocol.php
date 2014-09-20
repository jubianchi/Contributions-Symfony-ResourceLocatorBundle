<?php

namespace Hoathis\Bundle\ResourceLocatorBundle;

use Hoa\Core;

abstract class Protocol extends Core\Protocol\Library
{
    public function __construct($name = null, $reach = null)
    {
        if (false === is_array($reach)) {
            $reach = array($reach);
        }

        parent::__construct($name);

        $this->addReaches($reach);
    }

    public function getScheme()
    {
        return str_replace('://', '', $this->getName());
    }

    public function addReach($reach)
    {
        $this->addReaches(array($reach));
    }

    public function addReaches(array $reaches)
    {
        $this->setReach(
            array_merge(
                explode(';', $this->getReach()),
                array_map(
                    function($path) {
                        $path = preg_replace('#[^/]+/\.\./#', '', $path);

                        return $path;
                    },
                    $reaches
                )
            )
        );
    }

    public function setReach($reach)
    {
        if (is_array($reach)) {
            $reach = implode(';', array_reverse($reach));
        }

        return parent::setReach($reach);
    }

    public function getReach()
    {
        return parent::getReach();
    }

    public function reach($queue = null)
    {
        if (!empty($queue)) {
            $head = $queue;

            if (false !== $pos = strpos($queue, '/')) {
                $head  = substr($head, 0, $pos);
                $queue = DIRECTORY_SEPARATOR . substr($queue, $pos + 1);
            } else {
                $queue = null;
            }

            $out = array();

            foreach (explode(';', $this->_reach) as $part) {
                $_pos  = strrpos(rtrim($part, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR) + 1;
                $_head = substr($part, 0, $_pos);
                $_tail = rtrim(substr($part, $_pos), DIRECTORY_SEPARATOR);
                $out[] = $_head . $_tail . DIRECTORY_SEPARATOR . $head . $queue;
            }

            return implode(';', $out);
        }

        $out = array();

        foreach (explode(';', $this->_reach) as $part) {
            $pos   = strrpos(rtrim($part, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR) + 1;
            $head  = substr($part, 0, $pos);
            $tail  = substr($part, $pos);
            $out[] = $head . $tail;
        }

        $this->_reach = implode(';', $out);

        return parent::reach($queue);
    }

    public function resolve($path, $exists = true, $unfold = false)
    {
        $translated = str_replace($this->getName(), 'hoa://', $path);
        $resolved = parent::resolve($translated, $exists, $unfold);

        if (self::NO_RESOLUTION === $resolved || $resolved === false) {
            throw new \RuntimeException('Could not locate resource: ' . $path);
        }

        return $resolved;
    }
} 
