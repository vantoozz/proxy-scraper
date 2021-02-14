<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\ClarketmProxyListScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class ClarketmProxyListScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class ClarketmProxyListScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new ClarketmProxyListScraper(
            new FailingDummyHttpClient('error message')
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new ClarketmProxyListScraper(
            new PredefinedDummyHttpClient("222.111.222.111:8118\n111.222.111.222:8118")
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        self::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        self::assertSame(ClarketmProxyListScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new ClarketmProxyListScraper(
            new PredefinedDummyHttpClient("222.111.222.111:8118\n111.222.111.222:8118")
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        self::assertSame('222.111.222.111:8118', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        $scraper = new ClarketmProxyListScraper(
            new PredefinedDummyHttpClient('2312318')
        );

        self::assertNull($scraper->get()->current());
    }
}
