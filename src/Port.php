<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper;

use Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException;

/**
 * Class Port
 * @package Vantoozz\ProxyScrapper
 */
class Port
{
    const MIN_PORT_NUMBER = 1;
    const MAX_PORT_NUMBER = 65535;

    /**
     * @var int
     */
    private $port;

    /**
     * Port constructor.
     * @param int $port
     * @throws InvalidArgumentException
     */
    public function __construct(int $port)
    {
        if (self::MIN_PORT_NUMBER > $port) {
            throw new InvalidArgumentException('Bad port number: ' . $port);
        }
        if (self::MAX_PORT_NUMBER < $port) {
            throw new InvalidArgumentException('Bad port number: ' . $port);
        }
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->port;
    }
}
