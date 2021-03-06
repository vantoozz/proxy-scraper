<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\HttpClient;

use Exception;
use Http\Message\MessageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;
use Vantoozz\ProxyScraper\HttpClient\Psr18HttpClient;

/**
 * Class Psr18HttpClientTest
 * @package Vantoozz\ProxyScraper\UnitTests\HttpClient
 */
final class Psr18HttpClientTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_an_exception_if_an_error_happens(): void
    {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('error message');

        $messageFactory = $this->messageFactory(
            $this->createMock(RequestInterface::class),
            $this->createMock(ResponseInterface::class)
        );

        $client = new class implements ClientInterface {

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw new RuntimeException('error message');
            }
        };

        $httpClient = new Psr18HttpClient($client, $messageFactory);
        $httpClient->get('some url');
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_processing_the_request_is_impossible(): void
    {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('error message');

        $requestFactory = $this->messageFactory(
            $this->createMock(RequestInterface::class),
            $this->createMock(ResponseInterface::class)
        );

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

        $httpClient = new Psr18HttpClient($client, $requestFactory);
        $httpClient->get('some url');
    }

    /**
     * @test
     */
    public function it_returns_a_string(): void
    {
        $requestFactory = $this->messageFactory(
            $this->createMock(RequestInterface::class),
            $this->createMock(ResponseInterface::class)
        );

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


        $httpClient = new Psr18HttpClient($client, $requestFactory);

        self::assertEquals('some string', $httpClient->get('some url'));
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return MessageFactory
     */
    private function messageFactory(
        RequestInterface $request,
        ResponseInterface $response
    ): MessageFactory {
        return new class($request, $response) implements MessageFactory {

            /**
             * @var RequestInterface
             */
            private $request;
            /**
             * @var ResponseInterface
             */
            private $response;

            /**
             * @param RequestInterface $request
             * @param ResponseInterface $response
             */
            public function __construct(
                RequestInterface $request,
                ResponseInterface $response
            ) {
                $this->request = $request;
                $this->response = $response;
            }

            /**
             * @param $method
             * @param $uri
             * @param array $headers
             * @param null $body
             * @param string $protocolVersion
             * @return RequestInterface
             */
            public function createRequest(
                $method,
                $uri,
                array $headers = [],
                $body = null,
                $protocolVersion = '1.1'
            ): RequestInterface {
                return $this->request;
            }

            /**
             * @param int $statusCode
             * @param null $reasonPhrase
             * @param array $headers
             * @param null $body
             * @param string $protocolVersion
             * @return ResponseInterface
             */
            public function createResponse(
                $statusCode = 200,
                $reasonPhrase = null,
                array $headers = [],
                $body = null,
                $protocolVersion = '1.1'
            ): ResponseInterface {
                return $this->response;
            }
        };
    }
}
