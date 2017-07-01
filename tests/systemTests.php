<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\SystemTests;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\HttpClient\HttplugHttpClient;
use Vantoozz\ProxyScraper\Scrapers;
use Vantoozz\ProxyScraper\SystemTests\ProxiesMiner\Cached;
use Vantoozz\ProxyScraper\SystemTests\Reports\CountsReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\DuplicatesReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\ExclusivityReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\ReportsPipeline;

$httpClient = new HttplugHttpClient(
    new GuzzleAdapter(new GuzzleClient),
    new GuzzleMessageFactory
);

$container = new Container;
$container->delegate(new ReflectionContainer);
$container->add(HttpClientInterface::class, $httpClient, true);

$miner = new ProxiesMiner\ScrapersProxiesMiner;
foreach ([
             Scrapers\FoxToolsScraper::class,
             Scrapers\FreeProxyListScraper::class,
             Scrapers\HideMyIpScraper::class,
             Scrapers\MultiproxyScraper::class,
             Scrapers\ProxyDbScraper::class,
             Scrapers\SocksProxyScraper::class,
             Scrapers\SpysMeScraper::class,
             Scrapers\SslProxiesScraper::class,
             Scrapers\UsProxyScraper::class,
         ] as $class) {
    $miner->addScraper($container->get($class));
}
if (in_array('--cached', $argv, true)) {
    $miner = new Cached($miner, __DIR__ . '/.cached_proxies');
}

$pipeline = new ReportsPipeline;
$pipeline->addReport(new CountsReport);
$pipeline->addReport(new DuplicatesReport);
$pipeline->addReport(new ExclusivityReport);

$pipeline->run($miner->getProxies());