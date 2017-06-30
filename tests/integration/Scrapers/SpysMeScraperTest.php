<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\SpysMeScraper;

/**
 * Class SpysMeScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class SpysMeScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new SpysMeScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());

        $this->assertGreaterThanOrEqual(100, count($proxies));
    }
}
