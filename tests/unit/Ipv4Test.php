<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Ipv4;

/**
 * Class Ipv4Test
 * @package Vantoozz\ProxyScraper
 */
final class Ipv4Test extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_string(): void
    {
        static::assertSame('127.0.0.1', (string)new Ipv4('127.0.0.1'));
    }

    /**
     * @test
     */
    public function it_rejects_not_ip4v(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ipv4 string: some string');

        new Ipv4('some string');
    }

    /**
     * @test
     */
    public function it_rejects_ipv6_addresses(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid ipv4 string: 0:0:0:0:0:0:0:1');

        new Ipv4('0:0:0:0:0:0:0:1');
    }
}
