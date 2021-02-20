<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\SystemTests;

require_once __DIR__ . '/../vendor/autoload.php';

use hanneskod\classtools\Iterator\ClassIterator;
use Symfony\Component\Finder\Finder;
use Vantoozz\ProxyScraper\Scrapers\Discoverable;
use Vantoozz\ProxyScraper\Scrapers\ScraperInterface;
use Vantoozz\ProxyScraper\SystemTests\ProxiesMiner\Cached;
use Vantoozz\ProxyScraper\SystemTests\Reports\CountsReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\DuplicatesReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\ExclusivityReport;
use Vantoozz\ProxyScraper\SystemTests\Reports\ReportsPipeline;

use function Vantoozz\ProxyScraper\guzzleHttpClient;

$httpClient = guzzleHttpClient();

$miner = new ProxiesMiner\ScrapersProxiesMiner;

$classIterator = new ClassIterator((new Finder)->in(__DIR__ . '/../src/Scrapers'));
foreach ($classIterator->type(Discoverable::class) as $class) {
    if (!$class->isInstantiable()) {
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
