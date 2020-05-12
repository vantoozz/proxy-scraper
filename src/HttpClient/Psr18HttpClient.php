<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\HttpClient;

use Exception;
use Http\Message\MessageFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as Client;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;

/**
 * Class Psr18HttpClient
 * @package Vantoozz\ProxyScraper
 */
final class Psr18HttpClient implements HttpClientInterface
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * Psr18HttpClient constructor.
     * @param Client $httpClient
     * @param MessageFactory $messageFactory
     */
    public function __construct(Client $httpClient, MessageFactory $messageFactory)
    {
        $this->httpClient = $httpClient;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param string $uri
     * @return string
     * @throws HttpClientException
     */
    public function get(string $uri): string
    {
        $request = $this->messageFactory->createRequest('GET', $uri);
        try {
            return $this->httpClient->sendRequest($request)->getBody()->getContents();
        } catch (ClientExceptionInterface | Exception $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
