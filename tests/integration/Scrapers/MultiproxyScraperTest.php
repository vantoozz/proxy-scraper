<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\MultiproxyScraper;

/**
 * Class MultiproxyScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class MultiproxyScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $this->markTestSkipped('Temporary unavailable');

        $scrapper = new MultiproxyScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        self::assertGreaterThanOrEqual(100, count($proxies));
    }
}
