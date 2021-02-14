<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\SslProxiesScraper;

/**
 * Class SslProxiesScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class SslProxiesScraperTest extends IntegrationTest
{
    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new SslProxiesScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        self::assertGreaterThanOrEqual(50, count($proxies));
    }
}
