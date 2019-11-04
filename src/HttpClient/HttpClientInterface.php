<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\HttpClient;

use Vantoozz\ProxyScraper\Exceptions\HttpClientException;

/**
 * Interface HttpClientInterface
 * @package Vantoozz\ProxyScraper\HttplugHttpClient
 */
interface HttpClientInterface
{
    /**
     * @param string $uri
     * @return string
     * @throws HttpClientException
     */
    public function get(string $uri): string;
}
