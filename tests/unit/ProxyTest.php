<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScrapper\Ipv4;
use Vantoozz\ProxyScrapper\Port;
use Vantoozz\ProxyScrapper\Proxy;

/**
 * Class ProxyTest
 * @package Vantoozz\ProxyScrapper
 */
final class ProxyTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_string(): void
    {
        $proxy = new Proxy(new Ipv4('192.168.0.1'), new Port(1234));
        $this->assertSame('192.168.0.1:1234', (string)$proxy);
    }
}
