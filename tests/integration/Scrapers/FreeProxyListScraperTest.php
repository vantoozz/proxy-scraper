<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\FreeProxyListScraper;

/**
 * Class FreeProxyListScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
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
        static::assertGreaterThanOrEqual(100, count($proxies));
    }
}
