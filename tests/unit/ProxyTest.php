<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper;

use PHPUnit\Framework\TestCase;

/**
 * Class ProxyTest
 * @package Vantoozz\ProxyScrapper
 */
class ProxyTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_string(): void
    {
        $proxy = new Proxy(new Ipv4(99999999), new Port(1234));
        $this->assertSame('5.245.224.255:1234', (string)$proxy);
    }
}
