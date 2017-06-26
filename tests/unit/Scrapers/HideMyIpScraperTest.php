<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;
use Vantoozz\ProxyScrapper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScrapper\Proxy;
use Vantoozz\ProxyScrapper\Scrapers\MultiproxyScraper;
use Vantoozz\ProxyScrapper\Scrapers\HideMyIpScraper;

/**
 * Class HideMyIpScraperTest
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class HideMyIpScraperTest extends TestCase
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

        $scraper = new HideMyIpScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     * @expectedExceptionMessage Unknown markup
     */
    public function it_throws_an_exception_if_unknown_markdown_got(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
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
     * @expectedException \Vantoozz\ProxyScrapper\Exceptions\ScraperException
     * @expectedExceptionMessage Cannot parse json: Syntax error
     */
    public function it_throws_an_exception_if_bad_json_got(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
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
    public function it_returns_a_proxy(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn(file_get_contents(__DIR__.'/../../fixtures/hideMyIp.html'));

        $scraper = new HideMyIpScraper($httpClient);
        $proxy = $scraper->get()->current();

        $this->assertInstanceOf(Proxy::class, $proxy);
        $this->assertSame('104.41.154.213:8118', (string)$proxy);
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
            ->willReturn('var json = [{"i":"2323","p":"2323"}] ');

        $scraper = new HideMyIpScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_bad_formatted_json_items(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('var json = [1,2,3] ');

        $scraper = new HideMyIpScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }

    /**
     * @test
     */
    public function it_skips_not_filled_json_items(): void
    {
        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn('var json = [{"a":1}] ');

        $scraper = new HideMyIpScraper($httpClient);

        $this->assertNull($scraper->get()->current());
    }
}
