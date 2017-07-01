<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\SystemTests\Reports;

final class ExclusivityReport implements ReportInterface
{
    /**
     * @param array $proxies
     */
    public function run(array $proxies): void
    {
        $exclusivity = [];
        $sources = array_keys($proxies);
        foreach ($sources as $source) {
            $exclusivity[$source] = $this->calculateExclusivity($proxies, $source);
        }

        arsort($exclusivity);

        echo '==EXCLUSIVITY==' . "\n";
        foreach ($exclusivity as $source => $value) {
            echo $source . ' => ' . $value;
            if (0 !== count($proxies[$source])) {
                echo ' (' . round(100 * $value / count($proxies[$source])) . '%)';
            }
            echo "\n";
        }

        echo "\n";
        echo "\n";
    }

    /**
     * @param array $proxies
     * @param string $source
     * @return int
     */
    private function calculateExclusivity(array $proxies, string $source): int
    {
        $sourceProxies = $proxies[$source];
        unset($proxies[$source]);
        $otherProxies = [];
        foreach ($proxies as $otherProxy) {
            $otherProxies += $otherProxy;
        }

        $uniqueCount = count($sourceProxies);
        foreach ($sourceProxies as $ipv4 => $ports) {
            if (!isset($otherProxies[$ipv4])) {
                continue;
            }
            foreach ($ports as $port => $proxy) {
                if (!isset($otherProxies[$ipv4][$port])) {
                    continue;
                }
                $uniqueCount--;
            }
        }

        return $uniqueCount;
    }
}