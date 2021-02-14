<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\HttpClient;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\PsrHttpClient;

/**
 * Class PsrHttpClientTest
 * @package Vantoozz\ProxyScraper\UnitTests\HttpClient
 */
final class PsrHttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_if_an_error_happens(): void
    {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('error message');

        $requestFactory = $this->requestFactory($this->createMock(RequestInterface::class));

        $client = new class implements ClientInterface {

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new RuntimeException('error message');
            }
        };

        $httpClient = new PsrHttpClient($client, $requestFactory);
        $httpClient->get('some url');
    }

    /**
     * @param RequestInterface $request
     * @return RequestFactoryInterface
     */
    private function requestFactory(RequestInterface $request): RequestFactoryInterface
    {
        return new class($request) implements RequestFactoryInterface {

            /**
             * @var RequestInterface
             */
            private $request;

            /**
             *  constructor.
             * @param RequestInterface $request
             */
            public function __construct(RequestInterface $request)
            {
                $this->request = $request;
            }

            /**
             * @param string $method
             * @param $uri
             * @return RequestInterface
             */
            public function createRequest(string $method, $uri): RequestInterface
            {
                return $this->request;
            }
        };
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_processing_the_request_is_impossible(): void
    {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('error message');

        $requestFactory = $this->requestFactory($this->createMock(RequestInterface::class));

        $client = new class implements ClientInterface {
            /**
             * @param RequestInterface $request
             * @return ResponseInterface
             */
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new class ('error message') extends Exception implements ClientExceptionInterface {
                };
            }
        };

        $httpClient = new PsrHttpClient($client, $requestFactory);
        $httpClient->get('some url');
    }

    /**
     * @test
     */
    public function it_returns_a_string(): void
    {
        $requestFactory = $this->requestFactory($this->createMock(RequestInterface::class));

        /** @var ResponseInterface|MockObject $requestFactory $response */
        $response = $this->createMock(ResponseInterface::class);

        /** @var StreamInterface|MockObject $requestFactory $body */
        $body = $this->createMock(StreamInterface::class);

        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn($body);

        $body
            ->expects(self::once())
            ->method('getContents')
            ->willReturn('some string');

        $client = new class($response) implements ClientInterface {

            /**
             * @var ResponseInterface
             */
            private $response;

            /**
             * @param ResponseInterface $response
             */
            public function __construct(ResponseInterface $response)
            {
                $this->response = $response;
            }

            /**
             * @param RequestInterface $request
             * @return ResponseInterface
             */
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };


        $httpClient = new PsrHttpClient($client, $requestFactory);

        self::assertEquals('some string', $httpClient->get('some url'));
    }
}
