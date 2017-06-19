<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper;

use PHPUnit\Framework\TestCase;

/**
 * Class PortTest
 * @package Vantoozz\ProxyScrapper
 */
class PortTest extends TestCase
{
    /**
     * @test
     * @expectedExceptionMessage Bad port number: -1
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_negative_port_number(): void
    {
        new Port(-1);
    }

    /**
     * @test
     * @expectedExceptionMessage Bad port number: 0
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_zero_as_port_number(): void
    {
        new Port(0);
    }

    /**
     * @test
     * @expectedExceptionMessage Bad port number: 999999
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\InvalidArgumentException
     */
    public function it_rejects_too_large_port_number(): void
    {
        new Port(999999);
    }
}
