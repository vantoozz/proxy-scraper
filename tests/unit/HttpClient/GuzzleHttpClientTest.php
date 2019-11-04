<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\HttpClient;

use GuzzleHttp\ClientInterface as Guzzle;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\GuzzleHttpClient;

/**
 * Class GuzzleHttpClientTest
 * @package Vantoozz\ProxyScraper\UnitTests\HttpClient
 */
final class GuzzleHttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_a_string(): void
    {
        /** @var Guzzle|\PHPUnit\Framework\MockObject\MockObject $guzzle */
        $guzzle = $this->createMock(Guzzle::class);

        /** @var Response|\PHPUnit\Framework\MockObject\MockObject $response */
        $response = $this->createMock(Response::class);

        /** @var StreamInterface|\PHPUnit\Framework\MockObject\MockObject $body */
        $body = $this->createMock(StreamInterface::class);

        $guzzle
            ->expects(static::once())
            ->method('request')
            ->willReturn($response);

        $response
            ->expects(static::once())
            ->method('getBody')
            ->willReturn($body);

        $body
            ->expects(static::once())
            ->method('getContents')
            ->willReturn('some string');

        $client = new GuzzleHttpClient($guzzle);
        static::assertSame('some string', $client->get('http://google.com'));
    }

    /**
     * @test
     */
    public function it_returns_a_proxied_string(): void
    {
        /** @var Guzzle|\PHPUnit\Framework\MockObject\MockObject $guzzle */
        $guzzle = $this->createMock(Guzzle::class);

        /** @var Response|\PHPUnit\Framework\MockObject\MockObject $response */
        $response = $this->createMock(Response::class);

        /** @var StreamInterface|\PHPUnit\Framework\MockObject\MockObject $body */
        $body = $this->createMock(StreamInterface::class);

        $guzzle
            ->expects(static::once())
            ->method('request')
            ->willReturn($response);

        $response
            ->expects(static::once())
            ->method('getBody')
            ->willReturn($body);

        $body
            ->expects(static::once())
            ->method('getContents')
            ->willReturn('some string');

        $client = new GuzzleHttpClient($guzzle);
        static::assertSame('some string', $client->getProxied('http://google.com', 'proxy'));
    }

    /**
     * @test
     */
    public function it_throws_http_exception(): void
    {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('error message');

        /** @var Guzzle|\PHPUnit\Framework\MockObject\MockObject $guzzle */
        $guzzle = $this->createMock(Guzzle::class);

        $guzzle
            ->expects(static::once())
            ->method('request')
            ->willThrowException(new \RuntimeException('error message'));

        $client = new GuzzleHttpClient($guzzle);
        $client->get('http://google.com');
    }
}
