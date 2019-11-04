<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper;

use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;

/**
 * Class ProxyFactory
 * @package Vantoozz\ProxyScraper
 */
final class ProxyString
{
    /**
     * @var Proxy
     */
    private $proxy;

    /**
     * @param string $string
     * @throws InvalidArgumentException
     */
    public function __construct(string $string)
    {
        $expectedPartsCount = 2;
        $parts = explode(':', $string);
        if ($expectedPartsCount !== count($parts)) {
            throw new InvalidArgumentException('Bad formatted proxy string');
        }

        [$ipv4, $port] = $parts;

        $this->proxy = new Proxy(new Ipv4($ipv4), new Port((int)$port));
    }

    /**
     * @return Proxy
     */
    public function asProxy(): Proxy
    {
        return $this->proxy;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->proxy;
    }
}
