<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\SystemTests\Reports;

final class CountsReport implements ReportInterface
{
    /**
     * @param array $proxies
     */
    public function run(array $proxies): void
    {
        $counts = [];

        foreach ($proxies as $source => $data) {
            $counts[$source] = count($data);
        }

        arsort($counts);

        echo '==COUNTS==' . "\n";
        foreach ($counts as $source => $count) {
            echo $source . ' => ' . $count . "\n";
        }

        echo "\n";
        echo "\n";
    }
}