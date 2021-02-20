<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Scrapers\FreeProxyListScraper;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = guzzleHttpClient();

$scraper = new FreeProxyListScraper($httpClient);

/** @var Proxy $proxy */
$proxy = $scraper->get()->current();

foreach ($proxy->getMetrics() as $metric) {
    echo $metric->getName() . ': ' . $metric->getValue() . "\n";
}
