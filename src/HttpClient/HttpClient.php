<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\HttpClient;

use Http\Client\Exception as ClientException;
use Http\Client\HttpClient as Client;
use Http\Message\MessageFactory;
use Vantoozz\ProxyScrapper\Enums\Http;
use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;

/**
 * Class HttpClient
 * @package Vantoozz\ProxyScrapper
 */
final class HttpClient implements HttpClientInterface
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
     * HttpClient constructor.
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
     * @throws \Vantoozz\ProxyScrapper\Exceptions\HttpClientException
     */
    public function get(string $uri): string
    {
        $request = $this->messageFactory->createRequest(Http::GET, $uri);
        try {
            return $this->httpClient->sendRequest($request)->getBody()->getContents();
        } catch (ClientException  $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
