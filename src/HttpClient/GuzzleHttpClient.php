<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\HttpClient;

use GuzzleHttp\ClientInterface as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Vantoozz\ProxyScraper\Exceptions\HttpClientException;

/**
 * Class GuzzleHttpClient
 * @package Vantoozz\ProxyScraper\HttpClient
 * @deprecated Use Psr18HttpClient instead
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
     * @throws HttpClientException
     */
    public function get(string $uri): string
    {
        $options = [];

        try {
            $data = $this->guzzle->request('GET', $uri, $options)->getBody()->getContents();
        } catch (GuzzleException | RuntimeException | ClientException $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e);
        }
        return $data;
    }
}
