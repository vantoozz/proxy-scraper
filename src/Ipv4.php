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
     * @var string
     */
    private $ipv4;

    /**
     * Ipv4 constructor.
     * @param string $ipv4
     * @throws InvalidArgumentException
     */
    public function __construct(string $ipv4)
    {
        if (!filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidArgumentException('Invalid ipv4 string: ' . $ipv4);
        }
        $this->ipv4 = $ipv4;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->ipv4;
    }
}
