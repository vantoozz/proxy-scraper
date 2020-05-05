<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\HttpClient;

use Exception;
use Http\Client\Exception as ClientException;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;

/**
 * Class HttplugHttpClientTest
 * @package Vantoozz\ProxyScraper\UnitTests\HttpClient
 */
final class HttplugHttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_if_an_error_happens(): void
    {
        $this->expectExceptionMessage('error message');
        $this->expectException(HttpClientException::class);

        /** @var RequestInterface|MockObject $request */
        $request = $this->createMock(RequestInterface::class);

        /** @var MessageFactory|MockObject $messageFactory */
        $messageFactory = $this->createMock(MessageFactory::class);
        $messageFactory
            ->expects(static::once())
            ->method('createRequest')
            ->willReturn($request);

        /** @var HttpClient|MockObject $client */
        $client = $this->createMock(HttpClient::class);
        $client
            ->expects(static::once())
            ->method('sendRequest')
            ->willThrowException(new Exception('error message'));

        $httpClient = new HttplugHttpClient($client, $messageFactory);
        $httpClient->get('some url');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_processing_the_request_is_impossible(): void
    {
        $this->expectExceptionMessage('error message');
        $this->expectException(HttpClientException::class);

        /** @var RequestInterface|MockObject $messageFactory $request */
        $request = $this->createMock(RequestInterface::class);

        /** @var MessageFactory|MockObject $messageFactory */
        $messageFactory = $this->createMock(MessageFactory::class);
        $messageFactory
            ->expects(static::once())
            ->method('createRequest')
            ->willReturn($request);

        /** @var HttpClient|MockObject $client */
        $client = $this->createMock(HttpClient::class);
        $client
            ->expects(static::once())
            ->method('sendRequest')
            ->willThrowException(new class ('error message') extends Exception implements ClientException {
            });

        $httpClient = new HttplugHttpClient($client, $messageFactory);
        $httpClient->get('some url');
    }

    /**
     * @test
     */
    public function it_returns_a_string(): void
    {
        /** @var RequestInterface|MockObject $messageFactory $request */
        $request = $this->createMock(RequestInterface::class);

        /** @var ResponseInterface|MockObject $messageFactory $response */
        $response = $this->createMock(ResponseInterface::class);

        /** @var StreamInterface|MockObject $messageFactory $body */
        $body = $this->createMock(StreamInterface::class);

        /** @var MessageFactory|MockObject $messageFactory */
        $messageFactory = $this->createMock(MessageFactory::class);
        $messageFactory
            ->expects(static::once())
            ->method('createRequest')
            ->willReturn($request);

        /** @var HttpClient|MockObject $client */
        $client = $this->createMock(HttpClient::class);
        $client
            ->expects(static::once())
            ->method('sendRequest')
            ->willReturn($response);

        $response
            ->expects(static::once())
            ->method('getBody')
            ->willReturn($body);

        $body
            ->expects(static::once())
            ->method('getContents')
            ->willReturn('some string');

        $httpClient = new HttplugHttpClient($client, $messageFactory);

        static::assertEquals('some string', $httpClient->get('some url'));
    }
}
