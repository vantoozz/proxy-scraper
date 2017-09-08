<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\IntegrationTests;

use GuzzleHttp\Client as GuzzleClient;
use League\Container\Container;
use League\Container\ReflectionContainer;
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
     * @var Container
     */
    protected $container;

    /**
     *
     */
    public function setUp(): void
    {
        $this->container = $this->createContainer();
    }

    /**
     * @return Container
     */
    private function createContainer(): Container
    {
        $container = new Container;

        $container->delegate(new ReflectionContainer);

        $httpClient = new GuzzleHttpClient(new GuzzleClient([
            'connect_timeout' => 2,
            'timeout' => 3,
        ]));
        $container->add(HttpClientInterface::class, $httpClient, true);

        return $container;
    }

    /**
     * @return HttpClientInterface
     */
    protected function httpClient(): HttpClientInterface
    {
        return $this->container->get(HttpClientInterface::class);
    }
}