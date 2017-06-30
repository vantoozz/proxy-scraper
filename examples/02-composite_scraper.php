<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = new HttplugHttpClient(
    new GuzzleAdapter(new GuzzleClient),
    new GuzzleMessageFactory
);

$compositeScraper = new Scrapers\CompositeScraper;

$compositeScraper->addScraper(new Scrapers\FreeProxyListScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\MultiproxyScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\ProxyDbScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\SocksProxyScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\SpysMeScraper($httpClient));
$compositeScraper->addScraper(new Scrapers\UsProxyScraper($httpClient));

foreach ($compositeScraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
