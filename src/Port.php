<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper;

use Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException;

/**
 * Class Port
 * @package Vantoozz\ProxyScrapper
 */
final class Port
{
    private const MIN_PORT_NUMBER = 1;
    private const MAX_PORT_NUMBER = 65535;

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
    public function __toString(): string
    {
        return (string)$this->port;
    }
}
