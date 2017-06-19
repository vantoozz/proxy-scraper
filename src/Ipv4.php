<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper;

use Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException;

/**
 * Class Ipv4
 * @package Vantoozz\ProxyScrapper
 */
class Ipv4
{
    /**
     * @var int
     */
    private $ipv4;

    /**
     * Ipv4 constructor.
     * @param int $ipv4
     * @throws InvalidArgumentException
     */
    public function __construct(int $ipv4)
    {
        //TODO: check if ip address is valid
        $this->ipv4 = $ipv4;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return long2ip($this->ipv4);
    }
}
