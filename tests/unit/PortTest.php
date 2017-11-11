<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Port;

/**
 * Class PortTest
 * @package Vantoozz\ProxyScraper
 */
final class PortTest extends TestCase
{
    /**
     * @test
     * @expectedExceptionMessage Bad port number: -1
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_negative_port_number(): void
    {
        new Port(-1);
    }

    /**
     * @test
     * @expectedExceptionMessage Bad port number: 0
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_zero_as_port_number(): void
    {
        new Port(0);
    }

    /**
     * @test
     * @expectedExceptionMessage Bad port number: 999999
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_too_large_port_number(): void
    {
        new Port(999999);
    }

    /**
     * @test
     */
    public function it_converts_to_string(): void
    {
        static::assertSame('1234', (string)new Port(1234));
    }
}
