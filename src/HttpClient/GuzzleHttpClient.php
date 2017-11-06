<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\HttpClient;

use GuzzleHttp\ClientInterface as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Vantoozz\ProxyScraper\Enums\Http;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;

/**
 * Class GuzzleHttpClient
 * @package Vantoozz\ProxyScraper\HttpClient
 */
final class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Guzzle
     */
    private $guzzle;

    /**
     * GuzzleHttpClient constructor.
     * @param Guzzle $guzzle
     */
    public function __construct(Guzzle $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * @param string $uri
     * @return string
     * @throws \Vantoozz\ProxyScraper\Exceptions\HttpClientException
     */
    public function get(string $uri): string
    {
        return $this->request($uri);
    }

    /**
     * @param string $uri
     * @param string $proxy
     * @return string
     * @throws \Vantoozz\ProxyScraper\Exceptions\HttpClientException
     */
    public function getProxied(string $uri, string $proxy): string
    {
        return $this->request($uri, $proxy);
    }

    /**
     * @param string $uri
     * @param string $proxy
     * @return string
     * @throws \Vantoozz\ProxyScraper\Exceptions\HttpClientException
     */
    private function request(string $uri, string $proxy = null): string
    {
        $options = [];

        if (null !== $proxy) {
            $options['proxy'] = 'tcp://' . $proxy;
        }

        try {
            $data = $this->guzzle->request(Http::GET, $uri, $options)->getBody()->getContents();
        } catch (GuzzleException | \RuntimeException | ClientException $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }
        return $data;
    }
}
