<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;

/**
 * Class ProxyTest
 * @package Vantoozz\ProxyScraper
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

    /**
     * @test
     */
    public function it_returns_ipv4(): void
    {
        $ipv4 = new Ipv4('192.168.0.1');
        $proxy = new Proxy($ipv4, new Port(1234));
        $this->assertSame($ipv4, $proxy->getIpv4());
    }
}
