<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\SystemTests;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use hanneskod\classtools\Iterator\ClassIterator;
use Http\Adapter\Guzzle6\Client as HttpAdapter;
use Http\Message\MessageFactory\GuzzleMessageFactory as MessageFactory;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Vantoozz\ProxyScraper\HttpClient\HttpClientInterface;
use Vantoozz\ProxyScraper\HttpClient\Psr18HttpClient;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;
use Vantoozz\ProxyScraper\SystemTests\ProxiesMiner\Cached;
use Vantoozz\ProxyScraper\SystemTests\Reports\CountsReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\DuplicatesReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\ExclusivityReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\ReportsPipeline;

$httpClient = new Psr18HttpClient(
    new HttpAdapter(new GuzzleClient([
        'connect_timeout' => 5,
        'timeout' => 10,
    ])),
    new MessageFactory
);

$miner = new ProxiesMiner\ScrapersProxiesMiner;

$classIterator = new ClassIterator((new Finder)->in(__DIR__ . '/../src/Scrapers'));
foreach ($classIterator->type(ScraperInterface::class) as $class) {
    /** @var ReflectionClass $class */
    if (!$class->isInstantiable()) {
        continue;
    }

    $constructor = $class->getConstructor();
    if (!$constructor) {
        continue;
    }

    $parameters = $constructor->getParameters();
    if (1 !== count($parameters)) {
        continue;
    }

    $dependency = $parameters[0]->getClass();
    if (!$dependency) {
        continue;
    }

    if (!$dependency->implementsInterface(HttpClientInterface::class)) {
        continue;
    }

    /** @var ScraperInterface $scraper */
    $scraper = $class->newInstance($httpClient);
    $miner->addScraper($scraper);
}


$cacheFilename = __DIR__ . '/.cached_proxies';
if (in_array('--refresh', $argv, true)) {
    @unlink($cacheFilename);
}
if (in_array('--cached', $argv, true)) {
    $miner = new Cached($miner, $cacheFilename);
}

$pipeline = new ReportsPipeline;
$pipeline->addReport(new CountsReport);
$pipeline->addReport(new DuplicatesReport);
$pipeline->addReport(new ExclusivityReport);

$pipeline->run($miner->getProxies());
