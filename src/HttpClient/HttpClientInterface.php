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
     */
    public function get(string $uri): string;
}
