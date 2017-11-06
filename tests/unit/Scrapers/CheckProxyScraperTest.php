<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\CheckProxyScraper;

/**
 * Class CheckProxyScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class CheckProxyScraperTest extends TestCase
{
    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\ScraperException
     * @expectedExceptionMessage error message
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willThrowException(new HttpClientException('error message'));

        $scraper = new CheckProxyScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn(json_encode([['addr' => '222.111.222.111:8118']]));

        $scraper = new CheckProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_makes_many_attempts(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::at(0))
            ->method('get')
            ->willReturn(json_encode([]));
        $httpClient
            ->expects(static::at(1))
            ->method('get')
            ->willReturn(json_encode([]));
        $httpClient
            ->expects(static::at(2))
            ->method('get')
            ->willReturn(json_encode([['addr' => '222.111.222.111:8118']]));

        $scraper = new CheckProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_ip_addresses(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn(json_encode([['addr' => 'some strind']]));

        $scraper = new CheckProxyScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn(json_encode([['one' => 'some strind']]));

        $scraper = new CheckProxyScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_data(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::any())
            ->method('get')
            ->willReturn(json_encode([123, 234]));

        $scraper = new CheckProxyScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_json(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::any())
            ->method('get')
            ->willReturn('some string');

        $scraper = new CheckProxyScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }
}
