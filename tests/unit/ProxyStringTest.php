<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Exceptions\InvalidArgumentException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;

/**
 * Class ProxyStringTest
 * @package Vantoozz\ProxyScraper
 */
final class ProxyStringTest extends TestCase
{
    /**
     * @test
     */
    public function it_converts_to_string(): void
    {
        $proxyString = new ProxyString('192.168.0.1:1234');
        self::assertSame('192.168.0.1:1234', (string)$proxyString);
    }

    /**
     * @test
     */
    public function it_converts_to_proxy(): void
    {
        $proxyString = new ProxyString('192.168.0.1:1234');
        self::assertInstanceOf(Proxy::class, $proxyString->asProxy());
        self::assertSame('192.168.0.1:1234', (string)$proxyString->asProxy());
    }

    /**
     * @test
     * @dataProvider proxiesDataProvider
     * @param string $string
     * @param bool $expected
     */
    public function is_creates_from_strings(string $string, bool $expected): void
    {
        try {
            new ProxyString($string);
            $created = true;
        } catch (InvalidArgumentException $e) {
            $created = false;
        }

        self::assertEquals($created, $expected);
    }

    /**
     * @return array
     */
    public function proxiesDataProvider(): array
    {
        return [
            ['127.0.0.1:8080', true],
            ['127.0.0.1:8080 some string', true],
            ['127.0.0.1 8080', false],
            ['127.0.0.1 aaa', false],
            ['127:8080', false],
        ];
    }
}
