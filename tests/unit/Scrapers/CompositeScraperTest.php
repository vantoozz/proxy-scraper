<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\UnitTests\Scrapers;

use Generator;
use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Exceptions\ScraperException;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\ProxyString;
use Vantoozz\ProxyScraper\Scrapers\CompositeScraper;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;

/**
 * Class CompositeScraperTest
 * @package Vantoozz\ProxyScraper\UnitTests\Scrapers
 */
final class CompositeScraperTest extends TestCase
{
    /**
     * @test
     */
    public function it_calls_inner_scrapers(): void
    {
        $compositeScraper = new CompositeScraper();
        $compositeScraper->addScraper(new class implements ScraperInterface
        {
            public function get(): Generator
            {
                yield (new ProxyString('127.0.0.1:8080'))->asProxy();
            }
        });

        $compositeScraper->addScraper(new class implements ScraperInterface
        {
            public function get(): Generator
            {
                yield (new ProxyString('127.0.0.2:8080'))->asProxy();
            }
        });

        $expected = ['127.0.0.1:8080', '127.0.0.2:8080'];
        $i = 0;
        foreach ($compositeScraper->get() as $proxy) {
            self::assertInstanceOf(Proxy::class, $proxy);
            self::assertSame($expected[$i++], (string)$proxy);
        }
    }

    /**
     * @test
     */
    public function it_throws_exceptions_from_inner_scrapers(): void
    {
        $this->expectException(ScraperException::class);
        $this->expectExceptionMessage('some error');

        $compositeScraper = new CompositeScraper();
        $compositeScraper->addScraper(new class implements ScraperInterface
        {
            /** @noinspection PhpInconsistentReturnPointsInspection */
            public function get(): Generator
            {
                throw new ScraperException('some error');
            }
        });
        $compositeScraper->get()->current();
    }

    /**
     * @test
     */
    public function it_handles_exceptions_from_inner_scrapper(): void
    {
        $compositeScraper = new CompositeScraper();
        $compositeScraper->addScraper(new class implements ScraperInterface
        {
            /** @noinspection PhpInconsistentReturnPointsInspection */
            public function get(): Generator
            {
                throw new ScraperException('some error');
            }
        });

        $handledErrorMessage = '';
        $compositeScraper->handleScraperExceptionWith(function (ScraperException $e) use (&$handledErrorMessage) {
            $handledErrorMessage = $e->getMessage();
        });

        $compositeScraper->get()->current();

        self::assertSame('some error', $handledErrorMessage);
    }
}
