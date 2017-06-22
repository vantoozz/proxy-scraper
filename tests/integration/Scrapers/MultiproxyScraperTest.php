<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScrapper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScrapper\Scrapers\MultiproxyScraper;

/**
 * Class MultiproxyScraperTest
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class MultiproxyScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new MultiproxyScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());

        $this->assertGreaterThanOrEqual(100, count($proxies));
    }
}
