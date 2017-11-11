<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\CoolProxyScraper;
use Vantoozz\ProxyScraper\Scrapers\FoxToolsScraper;

/**
 * Class CoolProxyScraperTest
 * @package Vantoozz\ProxyScraper\Scrapers
 */
final class CoolProxyScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new CoolProxyScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        static::assertGreaterThanOrEqual(100, count($proxies));
    }
}
