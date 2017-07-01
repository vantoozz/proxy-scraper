<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\SystemTests\Reports;

/**
 * Interface ReportInterface
 * @package Vantoozz\ProxyScraper\SystemTests\Reports
 */
interface ReportInterface
{
    /**
     * @param array $proxies
     */
    public function run(array $proxies): void;
}