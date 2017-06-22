<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\HttpClient;

/**
 * Interface HttpClientInterface
 * @package Vantoozz\ProxyScrapper\HttplugHttpClient
 */
interface HttpClientInterface
{
    /**
     * @param string $uri
     * @return string
     */
    public function get(string $uri): string;
}
