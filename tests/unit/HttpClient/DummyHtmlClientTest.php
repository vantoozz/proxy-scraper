<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\HttpClient;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\HttpClient\DummyHtmlClient;

/**
 * Class DummyHtmlClientTest
 * @package Vantoozz\ProxyScraper\UnitTests\HttpClient
 */
final class DummyHtmlClientTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_the_given_value(): void
    {
        static::assertSame('some string', (new DummyHtmlClient('some string'))->get('any url'));
    }

}
