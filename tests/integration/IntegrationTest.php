<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\HttpClient\GuzzleHttpClient;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;

/**
 * Class IntegrationTest
 * @package Vantoozz\ProxyScraper
 */
abstract class IntegrationTest extends TestCase
{
    /**
     * @return HttpClientInterface
     */
    protected function httpClient(): HttpClientInterface
    {
        return new GuzzleHttpClient(new GuzzleClient([
            'connect_timeout' => 2,
            'timeout' => 3,
        ]));
    }
}
