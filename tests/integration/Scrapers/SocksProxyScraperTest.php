<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\SocksProxyScraper;

/**
 * Class SocksProxyScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class SocksProxyScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new SocksProxyScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());
        static::assertGreaterThanOrEqual(50, count($proxies));
    }
}
