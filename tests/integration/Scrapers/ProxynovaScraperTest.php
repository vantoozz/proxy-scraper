<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\ProxynovaScraper;
use Vantoozz\ProxyScraper\Scrapers\UsProxyScraper;

/**
 * Class ProxynovaScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class ProxynovaScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new ProxynovaScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get());
        static::assertGreaterThanOrEqual(100, count($proxies));
    }
}
