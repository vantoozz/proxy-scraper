<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\FoxToolsScraper;
use Vantoozz\ProxyScraper\Scrapers\PrimeSpeedScraper;

/**
 * Class PrimeSpeedScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class PrimeSpeedScraperTest extends TestCase
{
    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\ScraperException
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

        $scraper = new PrimeSpeedScraper($httpClient);
        $scraper->get()->current();
    }

    /**
     * @test
     */
    public function it_returns_a_proxy(): void
    {

        $html  = <<<HTML
<pre>
format:
&lt;proxy_server_name&gt; : &lt;proxy_port_number&gt;

0.0.0.0:80
222.111.222.111:8118
222.111.222.122:8118



</pre>

HTML;


        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn($html);

        $scraper = new PrimeSpeedScraper($httpClient);
        $proxies = iterator_to_array($scraper->get(), false);

        $this->assertInstanceOf(Proxy::class, $proxies[0]);
        $this->assertSame('222.111.222.111:8118', (string)$proxies[0]);
    }


    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\ScraperException
     * @expectedExceptionMessage Unexpected markup
     */
    public function it_throws_an_exception_on_unexpected_markup(): void
    {
        $html  = <<<HTML
<pre>
</pre>
HTML;


        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn($html);

        $scraper = new PrimeSpeedScraper($httpClient);
        $scraper->get()->current();
    }


    /**
     * @test
     * @expectedException \Vantoozz\ProxyScraper\Exceptions\ScraperException
     * @expectedExceptionMessage Unexpected markup
     */
    public function it_throws_more_exceptions_on_unexpected_markup(): void
    {
        $html  = <<<HTML
<pre>
format:
&lt;proxy_server_name&gt; : &lt;proxy_port_number&gt;

0.0.0.0:80
222.111.222.111:8118
222.111.222.122:8118

HTML;


        /** @var HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects(static::once())
            ->method('get')
            ->willReturn($html);

        $scraper = new PrimeSpeedScraper($httpClient);
        $scraper->get()->current();
    }

}
