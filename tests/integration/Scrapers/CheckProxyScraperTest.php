<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\CheckProxyScraper;

/**
 * Class CheckProxyScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class CheckProxyScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new CheckProxyScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        static::assertGreaterThanOrEqual(100, count($proxies));
    }
}
