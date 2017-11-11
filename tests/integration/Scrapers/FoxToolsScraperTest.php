<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\FoxToolsScraper;

/**
 * Class FoxToolsScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class FoxToolsScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new FoxToolsScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());

        static::assertGreaterThanOrEqual(80, count($proxies));
    }
}
