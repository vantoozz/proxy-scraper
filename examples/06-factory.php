<?php declare(strict_types=1);

use Vantoozz\ProxyScraper\ScrapersBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$allScrapers = (new ScrapersBuilder)->auto();

foreach ($allScrapers->get() as $proxy) {
    echo $proxy . "\n";
}
