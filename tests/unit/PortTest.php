<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Port;

/**
 * Class PortTest
 * @package Vantoozz\ProxyScraper
 */
final class PortTest extends TestCase
{
    /**
     * @test
     */
    public function it_rejects_negative_port_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad port number: -1');

        new Port(-1);
    }

    /**
     * @test
     */
    public function it_rejects_zero_as_port_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad port number: 0');

        new Port(0);
    }

    /**
     * @test
     */
    public function it_rejects_too_large_port_number(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad port number: 999999');

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
