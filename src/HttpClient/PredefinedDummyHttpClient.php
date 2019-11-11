<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\HttpClient;

/**
 * Class PredefinedDummyHttpClient
 * @package Vantoozz\ProxyScraper\HttpClient
 */
final class PredefinedDummyHttpClient implements HttpClientInterface
{

    /**
     * @var string
     */
    private $response;

    /**
     * PredefinedDummyHttpClient constructor.
     * @param string $response
     */
    public function __construct(string $response)
    {
        $this->response = $response;
    }

    /**
     * @param string $uri
     * @return string
     */
    public function get(string $uri): string
    {
        return $this->response;
    }
}
