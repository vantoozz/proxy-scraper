<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScrapper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScrapper\Scrapers\UsProxyScraper;

/**
 * Class UsProxyScraperTest
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class UsProxyScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new UsProxyScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());
        $this->assertGreaterThanOrEqual(100, count($proxies));
    }
}
