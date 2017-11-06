<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\CheckProxyScraper;
use Vantoozz\ProxyScraper\Scrapers\CoolProxyScraper;
use Vantoozz\ProxyScraper\Scrapers\FoxToolsScraper;

/**
 * Class CheckProxyScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
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

        $this->assertGreaterThanOrEqual(100, count($proxies));
    }
}
