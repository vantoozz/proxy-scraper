<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use Generator;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\Distinct;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class DistinctTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class DistinctTest extends TestCase
{

    /**
     * @test
     */
    public function it_filters_out_repeating_proxies(): void
    {
        $scraper = new class implements ScraperInterface {
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

        /** @var Proxy[] $proxies */
        $proxies = iterator_to_array((new Distinct($scraper))->get(), true);
        static::assertCount(2, $proxies);
        static::assertNotSame((string)$proxies[0], (string)$proxies[1]);
    }
}
