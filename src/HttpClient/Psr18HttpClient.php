<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\HttpClient;

use Exception;
use Http\Message\MessageFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;

/**
 * Class Psr18HttpClient
 * @package Vantoozz\ProxyScraper
 * @deprecated Use \Vantoozz\ProxyScraper\HttpClient\PsrHttpClient instead
 */
final class Psr18HttpClient implements HttpClientInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * Psr18HttpClient constructor.
     * @param ClientInterface $client
     * @param MessageFactory $messageFactory
     */
    public function __construct(ClientInterface $client, MessageFactory $messageFactory)
    {
        $this->client = $client;
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
            return $this->client->sendRequest($request)->getBody()->getContents();
        } catch (ClientExceptionInterface | Exception $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
