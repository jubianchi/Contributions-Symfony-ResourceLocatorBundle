<?php

namespace Hoathis\Bundle\ResourceLocatorBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class LocatorCollector implements DataCollectorInterface
{
    const NAME = 'hoathis.locator';

    protected $data = array();

    public function __construct()
    {
        $this->reset();
    }

    public function reset()
    {
        $this->data = array(
            self::NAME => array(
                'resources' => array(),
                'protocols' => array(
                    'total' => 0
                ),
                'longest' => array(),
                'total' => 0
            )
        );
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        //$this->reset();
    }

    public function addResource($path, $resolved, $time)
    {
        $resource = array(
            'path' => $path,
            'resolved' => $resolved,
            'time' => $time,
        );

        if (isset($this->data[self::NAME]['resources'][$path])) {
            $this->data[self::NAME]['resources'][$path]['time'] += $resource['time'];
        } else {
            $this->data[self::NAME]['resources'][$path] = $resource;
        }

        $this->data[self::NAME]['total'] += $resource['time'];

        $protocol = explode('://', $path);
        $this->addProtocol($protocol[0], $time);

        if (empty($this->data[self::NAME]['longest']) || $time > $this->data[self::NAME]['longest']['time']) {
            $this->data[self::NAME]['longest'] = $resource;
        }
    }

    protected function addProtocol($protocol, $time)
    {
        if (false === isset($this->data[self::NAME]['protocols'][$protocol])) {
            $this->data[self::NAME]['protocols'][$protocol] = array(
                'total' => 0,
                'resources' => 0
            );
        }

        $this->data[self::NAME]['protocols'][$protocol]['time'] += $time;
        $this->data[self::NAME]['protocols']['total'] += $time;
        $this->data[self::NAME]['protocols'][$protocol]['resources']++;
    }

    public function getName()
    {
        return self::NAME;
    }

    public function getResources()
    {
        return $this->data[self::NAME]['resources'];
    }

    public function getLongest()
    {
        return $this->data[self::NAME]['longest'];
    }

    public function getTotal()
    {
        return $this->data[self::NAME]['total'];
    }

    public function getProtocols()
    {
        return $this->data[self::NAME]['protocols'];
    }

    public function formatTime($time)
    {
        if ($time < 1) {
            return sprintf('%.2f ms', $time * 1000);
        } else {
            return sprintf('%.2f s', $time);
        }
    }
}
