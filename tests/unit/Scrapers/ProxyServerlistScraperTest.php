<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\ProxyServerlistScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class ProxyServerlistScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class ProxyServerlistScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new ProxyServerlistScraper(
            new FailingDummyHttpClient('error message')
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_unknown_XML_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Invalid XML');

        $scraper = new ProxyServerlistScraper(
            new PredefinedDummyHttpClient('Invalid XML')
        );
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new ProxyServerlistScraper(
            new PredefinedDummyHttpClient(file_get_contents(__DIR__ . '/../../fixtures/topProxysBlogger.xml'))
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(ProxyServerlistScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new ProxyServerlistScraper(
            new PredefinedDummyHttpClient(file_get_contents(__DIR__ . '/../../fixtures/topProxysBlogger.xml'))
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('1.0.134.189:443', (string)$proxy);
    }
}
