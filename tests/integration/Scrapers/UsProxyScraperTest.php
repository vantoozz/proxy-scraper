<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\UsProxyScraper;

/**
 * Class UsProxyScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
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
