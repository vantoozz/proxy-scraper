<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers\Decorators;

use Generator;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\Decorators\Distinct;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class DistinctTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers\Decorators
 */
final class DistinctTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_proxy(): void
    {
        /** @var Proxy[] $proxies */
        $proxies = iterator_to_array((new Distinct($this->scraper()))->get(), true);

        self::assertInstanceOf(Proxy::class, $proxies[0]);
    }

    /**
     * @test
     */
    public function it_filters_out_repeating_proxies(): void
    {
        /** @var Proxy[] $proxies */
        $proxies = iterator_to_array((new Distinct($this->scraper()))->get(), true);
        self::assertCount(2, $proxies);
        self::assertNotSame((string)$proxies[0], (string)$proxies[1]);
    }

    /**
     * @return ScraperInterface
     */
    private function scraper(): ScraperInterface
    {
        return new class implements ScraperInterface {
            /**
             * @return Generator
             */
            public function get(): Generator
            {
                yield new Proxy(new Ipv4('123.123.123.123'), new Port(1234));
                yield new Proxy(new Ipv4('234.234.234.234'), new Port(2345));
                yield new Proxy(new Ipv4('123.123.123.123'), new Port(1234));
            }
        };
    }
}
