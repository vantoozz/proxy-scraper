<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\HttpClient;

use Http\Client\Exception as ClientException;
use Http\Client\HttpClient as Client;
use Http\Message\MessageFactory;
use Vantoozz\ProxyScrapper\Enums\Http;
use Vantoozz\ProxyScrapper\Exceptions\HttpClientException;

/**
 * Class HttplugHttpClient
 * @package Vantoozz\ProxyScrapper
 */
final class HttplugHttpClient implements HttpClientInterface
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
     * HttplugHttpClient constructor.
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
     * @param array $headers
     * @return string
     * @throws HttpClientException
     */
    public function get(string $uri, array $headers): string
    {
        $request = $this->messageFactory->createRequest(Http::GET, $uri, $headers);
        try {
            return $this->httpClient->sendRequest($request)->getBody()->getContents();
        } catch (ClientException  $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
