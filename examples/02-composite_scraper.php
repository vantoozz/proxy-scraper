<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Vantoozz\ProxyScraper\HttpClient\GuzzleHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = new GuzzleHttpClient(new GuzzleClient([
    'connect_timeout' => 3,
    'timeout' => 4,
]));

$compositeScraper = new Scrapers\CompositeScraper;

$compositeScraper->addScraper(new Scrapers\FreeProxyListScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\MultiproxyScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\SocksProxyScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\SpysMeScraper($httpClient));

foreach ($compositeScraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
