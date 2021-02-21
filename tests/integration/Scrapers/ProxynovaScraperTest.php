<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\ProxynovaScraper;

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
//        $this->markTestSkipped('cURL error 60: SSL certificate problem: certificate has expired');

        $scrapper = new ProxynovaScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        self::assertGreaterThanOrEqual(30, count($proxies));
    }
}
