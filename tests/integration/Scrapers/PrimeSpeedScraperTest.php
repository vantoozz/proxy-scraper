<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\PrimeSpeedScraper;
use Vantoozz\ProxyScraper\Scrapers\UsProxyScraper;

/**
 * Class PrimeSpeedScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class PrimeSpeedScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new PrimeSpeedScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());
        $this->assertGreaterThanOrEqual(100, count($proxies));
    }
}
