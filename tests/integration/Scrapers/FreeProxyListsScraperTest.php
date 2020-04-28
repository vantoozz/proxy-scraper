<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\IntegrationTests\Scrapers;

use Vantoozz\ProxyScraper\IntegrationTests\IntegrationTest;
use Vantoozz\ProxyScraper\Scrapers\FreeProxyListsScraper;

/**
 * Class FreeProxyListsScraperTest
 * @package Vantoozz\ProxyScraper\IntegrationTests\Scrapers
 */
final class FreeProxyListsScraperTest extends IntegrationTest
{

    /**
     * @test
     */
    public function it_works(): void
    {
        $scrapper = new FreeProxyListsScraper($this->httpClient());

        $proxies = iterator_to_array($scrapper->get(), false);

        static::assertGreaterThanOrEqual(50, count($proxies));
    }
}
