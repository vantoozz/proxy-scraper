<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\CoolProxyScraper;

/**
 * Class CoolProxyScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class CoolProxyScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_stops_on_http_client_error(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willThrowException(new HttpClientException('error message'));

        $scraper = new CoolProxyScraper($httpClient);
        static::assertNull($scraper->get()->current());
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
            ->willReturn('<table><tr><td>"ZGp3YwDmYwH3YwD4"</td><td>2222</td></tr></table>');

        $scraper = new CoolProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(CoolProxyScraper::class, $proxy->getMetrics()[0]->getValue());
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
            ->willReturn('<table><tr><td>"ZGp3YwDmYwH3YwD4"</td><td>2222</td></tr></table>');

        $scraper = new CoolProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('177.43.57.48:2222', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_fetches_no_more_than_100_pages(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn('<table><tr><td>"ZGp3YwDmYwH3YwD4"</td><td>2222</td></tr></table>');

        $scraper = new CoolProxyScraper($httpClient);
        $proxies = iterator_to_array($scraper->get(), false);

        static::assertCount(100, $proxies);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn('<table><tr><td>aaa</td><td>2222</td></tr></table>');

        $scraper = new CoolProxyScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }
}
