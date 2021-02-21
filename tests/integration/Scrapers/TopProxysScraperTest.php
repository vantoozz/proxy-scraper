<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\TopProxysScraper;

/**
 * Class TopProxysScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class TopProxysScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new TopProxysScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        self::assertGreaterThanOrEqual(100, count($proxies));
    }
}
