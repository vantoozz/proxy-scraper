<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Enums\Metrics;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\HideMyIpScraper;

/**
 * Class HideMyIpScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class HideMyIpScraperTest extends TestCase
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

        $scraper = new HideMyIpScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_unknown_markdown_got(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('Unknown markup');

        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('bad markup');

        $scraper = new HideMyIpScraper($httpClient);
        $scraper->get()->current();
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

        $scraper = new HideMyIpScraper($httpClient);
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
            ->willReturn(file_get_contents(__DIR__ . '/../../fixtures/hideMyIp.html'));

        $scraper = new HideMyIpScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        /** @var Proxy $proxy */
        static::assertSame(Metrics::SOURCE, $proxy->getMetrics()[0]->getName());
        static::assertSame(HideMyIpScraper::class, $proxy->getMetrics()[0]->getValue());
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
            ->willReturn(file_get_contents(__DIR__ . '/../../fixtures/hideMyIp.html'));

        $scraper = new HideMyIpScraper($httpClient);
        $proxy = $scraper->get()->current();

        static::assertInstanceOf(Proxy::class, $proxy);
        static::assertSame('218.161.1.189:3128', (string)$proxy);
    }

    /**
     * @test
     */
    public function it_skips_bad_rows(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('var json = [{"i":"2323","p":"2323"}] ');

        $scraper = new HideMyIpScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_formatted_json_items(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('var json = [1,2,3] ');

        $scraper = new HideMyIpScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_not_filled_json_items(): void
    {
        /** @var HttpClientInterface|MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('var json = [{"a":1}] ');

        $scraper = new HideMyIpScraper($httpClient);

        static::assertNull($scraper->get()->current());
    }
}
