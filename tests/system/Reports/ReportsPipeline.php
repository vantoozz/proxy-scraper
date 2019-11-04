<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\SystemTests\Reports;

/**
 * Class ReportsPipeline
 * @package Vantoozz\ProxyScraper\SystemTests\Reports
 */
final class ReportsPipeline implements ReportInterface
{
    /**
     * @var ReportInterface[]
     */
    private $reports = [];

    /**
     * @param ReportInterface $report
     */
    public function addReport(ReportInterface $report): void
    {
        $this->reports[] = $report;
    }

    /**
     * @param array $proxies
     */
    public function run(array $proxies): void
    {
        foreach ($this->reports as $report) {
            $report->run($proxies);
        }
    }
}
