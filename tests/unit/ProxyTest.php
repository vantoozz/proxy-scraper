<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Metric;
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
        static::assertSame('192.168.0.1:1234', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_returns_ipv4(): void
    {
        $ipv4 = new Ipv4('192.168.0.1');
        $proxy = new Proxy($ipv4, new Port(1234));
        static::assertSame($ipv4, $proxy->getIpv4());
    }

    /**
     * @test
     */
    public function it_returns_port(): void
    {
        $port = new Port(1234);
        $proxy = new Proxy(new Ipv4('192.168.0.1'), $port);
        static::assertSame($port, $proxy->getPort());
    }

    /**
     * @test
     */
    public function it_stores_metrics(): void
    {
        $one = new Metric('one', 111);
        $two = new Metric('two', 222);

        $proxy = new Proxy(new Ipv4('8.8.8.8'), new Port(8888));

        $proxy->addMetric($one);
        $proxy->addMetric($two);

        static::assertSame([$one, $two], $proxy->getMetrics());
    }
}
