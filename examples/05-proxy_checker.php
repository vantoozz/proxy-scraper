<?php declare(strict_types = 1);

use GuzzleHttp\Client as GuzzleClient;
use Vantoozz\ProxyScraper\Appraiser;
use Vantoozz\ProxyScraper\HttpClient\GuzzleHttpClient;
use Vantoozz\ProxyScraper\Scrapers;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = new GuzzleHttpClient(new GuzzleClient([
    'connect_timeout' => 5,
    'timeout' => 6,
]));

$scraper = new Scrapers\FreeProxyListScraper($httpClient);


// See https://github.com/vantoozz/whoami
$whoamiHost = getenv('WHOAMI_HOST');

if (!$whoamiHost) {
    exit('No whoami host given');
}
$proxyAppraiser = new Appraiser($httpClient, $whoamiHost);

$proxiesToCheck = 100;

foreach ($scraper->get() as $proxy) {
    echo (string)$proxy . " =>\n";

    foreach ($proxyAppraiser->appraise($proxy) as $metric) {
        echo "\t" . (string)$metric . "\n";
    }

    if (!--$proxiesToCheck) {
        break;
    }
}
