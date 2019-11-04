<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\SystemTests\Reports;

/**
 * Class DuplicatesReport
 * @package Vantoozz\ProxyScraper\SystemTests\Reports
 */
final class DuplicatesReport implements ReportInterface
{
    /**
     * @param array $proxies
     */
    public function run(array $proxies): void
    {
        $sources = array_keys($proxies);
        $duplicates = array_fill_keys($sources, []);

        foreach ($sources as $a) {
            foreach ($sources as $b) {
                if ($a === $b) {
                    continue;
                }
                $duplicates[$a][$b] = $this->duplicatesCount($proxies[$a], $proxies[$b]);
            }
        }

        echo '==DUPLICATES(%)==' . "\n";
        echo "\t";
        foreach ($sources as $a) {
            echo $a[0] . $a[1] . "\t";
        }
        echo "\n";

        foreach ($sources as $a) {
            echo $a[0] . $a[1] . "\t";
            foreach ($sources as $b) {
                $value = '-';
                if (isset($duplicates[$a][$b])) {
                    $value = 0 === count($proxies[$a]) ? '*' : round(100 * $duplicates[$a][$b] / count($proxies[$a]));
                }
                echo $value . "\t";
            }
            echo "\n";
        }

        echo "\n";
        echo "\n";
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    private function duplicatesCount(array $a, array $b): int
    {
        $duplicates = [];
        foreach ($a as $ipv4 => $ports) {
            if (!isset($b[$ipv4])) {
                continue;
            }
            foreach ($ports as $port => $proxy) {
                if (!isset($b[$ipv4][$port])) {
                    continue;
                }
                $duplicates[] = $proxy;
            }
        }

        return count($duplicates);
    }
}
