<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\HttpClient\PsrHttpClient;

/**
 * Class IntegrationTest
 * @package Vantoozz\ProxyScraper\IntegrationTests
 */
abstract class IntegrationTest extends TestCase
{
    /**
     * @return HttpClientInterface
     */
    protected function httpClient(): HttpClientInterface
    {
        return new PsrHttpClient(
            new Client([
                RequestOptions::CONNECT_TIMEOUT => 5,
                RequestOptions::TIMEOUT => 15,
            ]),
            new class implements RequestFactoryInterface {
                /**
                 * @param string $method
                 * @param $uri
                 * @return RequestInterface
                 */
                public function createRequest(
                    string $method,
                    $uri
                ): RequestInterface {
                    return new Request($method, $uri);
                }
            }
        );
    }
}
