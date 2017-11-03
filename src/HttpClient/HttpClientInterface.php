<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\HttpClient;

/**
 * Interface HttpClientInterface
 * @package Vantoozz\ProxyScraper\HttplugHttpClient
 */
interface HttpClientInterface
{
    /**
     * @param string $uri
     * @return string
     * @throws \Vantoozz\ProxyScraper\Exceptions\HttpClientException
     */
    public function get(string $uri): string;

    /**
     * @param string $uri
     * @param string $proxy
     * @return string
     * @throws \Vantoozz\ProxyScraper\Exceptions\HttpClientException
     * @deprecated
     */
    public function getProxied(string $uri, string $proxy): string;
}
