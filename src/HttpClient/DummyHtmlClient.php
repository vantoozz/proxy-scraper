<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\HttpClient;

/**
 * Class DummyHtmlClient
 * @package Vantoozz\ProxyScraper\HttpClient
 * @deprecated
 */
final class DummyHtmlClient implements HttpClientInterface
{
    /**
     * @var string
     */
    private $response;

    /**
     * DummyHtmlClient constructor.
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
