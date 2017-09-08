<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Metric;

/**
 * Class MetricTest
 * @package Vantoozz\ProxyScraper\UnitTests
 */
final class MetricTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_name(): void
    {
        $metric = new Metric('some_name', 123);
        static::assertSame('some_name', $metric->getName());
    }

    /**
     * @test
     */
    public function it_returns_value(): void
    {
        $metric = new Metric('some_name', 123);
        static::assertSame(123, $metric->getValue());
    }

    /**
     * @test
     */
    public function it_converts_to_a_string(): void
    {
        $metric = new Metric('some_name', 123);
        static::assertSame('some_name: 123', (string)$metric);
    }
}
