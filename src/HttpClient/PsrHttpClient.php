<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\HttpClient;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;

/**
 * Class PsrHttpClient
 * @package Vantoozz\ProxyScraper
 */
final class PsrHttpClient implements HttpClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * Psr18HttpClient constructor.
     * @param ClientInterface $client
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param string $uri
     * @return string
     * @throws HttpClientException
     */
    public function get(string $uri): string
    {
        $request = $this->requestFactory->createRequest('GET', $uri);
        try {
            return $this->client->sendRequest($request)->getBody()->getContents();
        } catch (ClientExceptionInterface | Exception $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
