<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
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
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willThrowException(new HttpClientException('error message'));

        $scraper = new CoolProxyScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('[{"ip":"177.43.57.48","port":2222},{"ip":"206.189.220.8","port":80}]');

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
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('[{"ip":"177.43.57.48","port":2222},{"ip":"206.189.220.8","port":80}]');

        $scraper = new CoolProxyScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('177.43.57.48:2222', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_rows_with_no_ip(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn('[{"port":2222}]');

        $scraper = new CoolProxyScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_non_array_rows(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn('[123]');

        $scraper = new CoolProxyScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_rows_with_no_port(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn('[{"ip":"177.43.57.48"}]');

        $scraper = new CoolProxyScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_bad_json_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Cannot parse json: Syntax error');

        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('var json = dcvsdjh');

        $scraper = new CoolProxyScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_no_data_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('No data');

        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('123');

        $scraper = new CoolProxyScraper($httpClient);
        $scraper->get()->current();
    }
}
