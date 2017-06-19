<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper;

/**
 * Class Proxy
 * @package Vantoozz\ProxyScrapper
 */
class Proxy
{
    /**
     * @var Ipv4
     */
    private $ipv4;

    /**
     * @var Port
     */
    private $port;

    /**
     * Proxy constructor.
     * @param Ipv4 $ipv4
     * @param Port $port
     */
    public function __construct(Ipv4 $ipv4, Port $port)
    {
        $this->ipv4 = $ipv4;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->ipv4 . ':' . (string)$this->port;
    }
}
