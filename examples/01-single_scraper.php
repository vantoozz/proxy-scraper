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

$scraper = new Scrapers\FreeProxyListScraper($httpClient);

foreach ($scraper->get() as $proxy) {
    echo (string)$proxy . "\n";
}
