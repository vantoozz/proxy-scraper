<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\HideMyIpScraper;

/**
 * Class HideMyIpScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class HideMyIpScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new HideMyIpScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());

        static::assertGreaterThanOrEqual(50, count($proxies));
    }
}
