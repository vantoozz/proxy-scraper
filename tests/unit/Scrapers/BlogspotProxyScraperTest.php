<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\BlogspotProxyScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class BlogspotProxyScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class BlogspotProxyScraperTest extends TestCase
{

    /**
     * @test
     */
    public function it_throws_an_exception_if_unknown_XML_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Invalid XML');

        $scraper = new BlogspotProxyScraper(new PredefinedDummyHttpClient('Invalid XML'));
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new BlogspotProxyScraper(
            new PredefinedDummyHttpClient(file_get_contents(__DIR__ . '/../../fixtures/topProxysBlogger.xml'))
        );

        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        self::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        self::assertSame(BlogspotProxyScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new BlogspotProxyScraper(
            new PredefinedDummyHttpClient(file_get_contents(__DIR__ . '/../../fixtures/topProxysBlogger.xml'))
        );
        $proxy = $scraper->get()->current();

        self::assertInstanceOf(Proxy::class, $proxy);
        self::assertSame('1.0.134.189:443', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_return_nothing_if_no_suitable_proxy_found(): void
    {
        $httpClient = new PredefinedDummyHttpClient(file_get_contents(__DIR__ . '/../../fixtures/noProxyBlogger.xml'));

        $proxies = iterator_to_array((new BlogspotProxyScraper($httpClient))->get(), true);

        self::assertCount(0, $proxies);
    }

}
