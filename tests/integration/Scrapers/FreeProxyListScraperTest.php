<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScrapper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScrapper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScrapper\Scrapers\FreeProxyListScraper;

/**
 * Class FreeProxyListScraperTest
 * @package Vantoozz\ProxyScrapper\Scrapers
 */
final class FreeProxyListScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new FreeProxyListScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());
        $this->assertGreaterThanOrEqual(300, count($proxies));
    }
}
