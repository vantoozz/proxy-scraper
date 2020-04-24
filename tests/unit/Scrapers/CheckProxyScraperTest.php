<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\CheckProxyScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class CheckProxyScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class CheckProxyScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new CheckProxyScraper(new FailingDummyHttpClient('error message'));
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new CheckProxyScraper(
            new PredefinedDummyHttpClient(json_encode([['addr' => '222.111.222.111:8118']]))
        );

        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(CheckProxyScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new CheckProxyScraper(
            new PredefinedDummyHttpClient(json_encode([['addr' => '222.111.222.111:8118']]))
        );

        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_makes_many_attempts(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
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

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_ip_addresses(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient(json_encode([['addr' => 'some strind']])));

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient(json_encode([['one' => 'some strind']])));

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_data(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient(json_encode([123, 234])));

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_json(): void
    {
        $scraper = new CheckProxyScraper(new PredefinedDummyHttpClient('some string'));

        static::assertNull($scraper->get()->current());
    }
}
