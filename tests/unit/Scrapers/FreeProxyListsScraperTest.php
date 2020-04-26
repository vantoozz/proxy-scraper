<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\FreeProxyListsScraper;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\FailingDummyHttpClient;
use Vantoozz\ProxyScraper\UnitTests\HttpClient\PredefinedDummyHttpClient;

/**
 * Class FreeProxyListsScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class FreeProxyListsScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_on_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');

        $scraper = new FreeProxyListsScraper(new FailingDummyHttpClient('error message'));
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_on_2nd_request_http_client_error(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('error message');


        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::at(0))
            ->method('get')
            ->willReturn('<p>elite/123.html</p>');
        $httpClient
            ->expects(static::at(1))
            ->method('get')
            ->willThrowException(new HttpClientException('error message'));


        $scraper = new FreeProxyListsScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_source_metric(): void
    {
        $scraper = new FreeProxyListsScraper(
            new PredefinedDummyHttpClient(
                '<p>elite/123.html</p>td&gt;91.134.221.168&lt;/td&gt;&lt;td&gt;80&lt;'
            )
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(FreeProxyListsScraper::class, $proxy->getMetrics()[0]->getValue());
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {
        $scraper = new FreeProxyListsScraper(
            new PredefinedDummyHttpClient(
                '<p>elite/123.html</p>td&gt;91.134.221.168&lt;/td&gt;&lt;td&gt;80&lt;'
            )
        );
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('91.134.221.168:80', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_proxies(): void
    {
        $scraper = new FreeProxyListsScraper(
            new PredefinedDummyHttpClient(
                '<p>elite/123.html</p>td&gt;91.134.221.168&lt;/td&gt;&lt;td&gt;000&lt;'
            )
        );

        static::assertNull($scraper->get()->current());
    }
}
