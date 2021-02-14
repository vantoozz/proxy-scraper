<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers\Decorators;


use Generator;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\Decorators\Timed;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class TimedTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers\Decorators
 */
final class TimedTest extends TestCase
{

    /**
     * @test
     */
    public function it_returns_proxy(): void
    {
        $events = [];
        /** @var Proxy[] $proxies */
        $proxies = iterator_to_array((new Timed($this->scraper(), $this->output($events)))->get());

        self::assertCount(2, $proxies);
        self::assertInstanceOf(Proxy::class, $proxies[0]);
    }

    /**
     * @test
     */
    public function it_emits_events(): void
    {
        $events = [];

        iterator_to_array((new Timed($this->scraper(), $this->output($events)))->get());

        self::assertCount(3, $events);
    }

    /**
     * @test
     */
    public function it_emits_done_events(): void
    {
        $events = [];

        iterator_to_array((new Timed($this->scraper(), $this->output($events)))->get());

        self::assertSame('done', $events[2][0]);
        self::assertIsFloat($events[2][1]);
    }

    /**
     * @test
     */
    public function it_emits_proxy_found_events(): void
    {
        $events = [];

        iterator_to_array((new Timed($this->scraper(), $this->output($events)))->get());

        self::assertSame('proxy_found', $events[0][0]);
        self::assertIsFloat($events[0][1]);
        self::assertSame('proxy_found', $events[1][0]);
        self::assertIsFloat($events[1][1]);
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
            }
        };
    }

    /**
     * @param array $events
     * @return Generator
     */
    private function output(array &$events): Generator
    {
        while ($event = yield) {
            $events[] = $event;
        }
    }
}
