<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScrapper\Ipv4;

/**
 * Class Ipv4Test
 * @package Vantoozz\ProxyScrapper
 */
final class Ipv4Test extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_string(): void
    {
        $this->assertSame('127.0.0.1', (string)new Ipv4('127.0.0.1'));
    }

    /**
     * @test
     * @expectedExceptionMessage Invalid ipv4 string: some string
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_not_ip4v(): void
    {
        new Ipv4('some string');
    }

    /**
     * @test
     * @expectedExceptionMessage Invalid ipv4 string: 0:0:0:0:0:0:0:1
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_ipv6_addresses(): void
    {
        new Ipv4('0:0:0:0:0:0:0:1');
    }
}
