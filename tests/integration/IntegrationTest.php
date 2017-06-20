<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\IntegrationTests;

use GuzzleHttp;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use League\Container\Container;
use League\Container\ReflectionContainer;
use PHPUnit\Framework\TestCase;

/**
 * Class IntegrationTest
 * @package Vantoozz\ProxyScrapper
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

        $container->add(GuzzleHttp\ClientInterface::class, GuzzleHttp\Client::class, true);
        $container->add(HttpClient::class, function () use ($container) {
            return new GuzzleAdapter($container->get(GuzzleHttp\ClientInterface::class), true);
        });
        $container->add(HttpAsyncClient::class, function () use ($container) {
            return new GuzzleAdapter($container->get(GuzzleHttp\ClientInterface::class), true);
        });
        $container->add(MessageFactory::class, GuzzleMessageFactory::class, true);

        return $container;
    }

    /**
     * @return \Vantoozz\ProxyScrapper\HttpClient
     */
    protected function httpClient(): \Vantoozz\ProxyScrapper\HttpClient
    {
        return $this->container->get(\Vantoozz\ProxyScrapper\HttpClient::class);
    }
}