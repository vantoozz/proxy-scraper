<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\BlogspotProxyScraper;

/**
 * Class BlogspotProxyScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class BlogspotProxyScraperTest extends TestCase
{

    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\ScraperException
     * @expectedExceptionMessage Invalid XML
     */
    public function it_throws_an_exception_if_unknown_XML_got(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('Invalid XML')
        ;

        $scraper = new BlogspotProxyScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn(file_get_contents(__DIR__ . '/../../fixtures/topProxysBlogger.xml'))
        ;

        $scraper = new BlogspotProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[ 0 ]->getName());
        static::assertSame(BlogspotProxyScraper::class, $proxy->getMetrics()[ 0 ]->getValue());
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
            ->willReturn(file_get_contents(__DIR__ . '/../../fixtures/topProxysBlogger.xml'))
        ;

        $scraper = new BlogspotProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('1.0.134.189:443', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_return_nothing_if_no_suitable_proxy_found(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn(file_get_contents(__DIR__ . '/../../fixtures/noProxyBlogger.xml'))
        ;

        $proxies = iterator_to_array((new BlogspotProxyScraper($httpClient))->get(), true);

        static::assertCount(0, $proxies);
    }

}