<?php declare(strict_types=1);

use function Vantoozz\ProxyScraper\proxyScraper;

require_once __DIR__ . '/../vendor/autoload.php';

$i = 0;

foreach (proxyScraper()->get() as $proxy) {
    echo $proxy . "\n";

    if(++$i >= 100){
        break;
    }
}
