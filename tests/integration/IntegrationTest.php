<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\HttpClient\Psr18HttpClient;

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
        return new Psr18HttpClient(
            new HttpAdapter(new GuzzleClient([
                'connect_timeout' => 5,
                'timeout' => 15,
            ])),
            new MessageFactory
        );
    }
}
