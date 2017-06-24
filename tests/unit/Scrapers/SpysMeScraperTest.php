<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;
use Vantoozz\ProxyScrapper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScrapper\Proxy;
use Vantoozz\ProxyScrapper\Scrapers\MultiproxyScraper;
use Vantoozz\ProxyScrapper\Scrapers\SpysMeScraper;

/**
 * Class SpysMeScraperTest
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class SpysMeScraperTest extends TestCase
{
    /**
     * @test
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\ScraperException
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

        $scraper = new SpysMeScraper($httpClient);
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
            ->willReturn("222.111.222.111:8118\n111.222.111.222:8118");

        $scraper = new SpysMeScraper($httpClient);
        $proxy = $scraper->get()->current();

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertSame('222.111.222.111:8118', (string)$proxy);
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
            ->willReturn('2312318');

        $scraper = new SpysMeScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }
}
