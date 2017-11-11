<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper\UnitTests\Validators;

use PHPUnit\Framework\TestCase;
use Vantoozz\ProxyScraper\Exceptions\ValidationException;
use Vantoozz\ProxyScraper\Ipv4;
use Vantoozz\ProxyScraper\Port;
use Vantoozz\ProxyScraper\Proxy;
use Vantoozz\ProxyScraper\Validators\Ipv4RangeValidator;

/**
 * Class Ipv4RangeValidatorTest
 * @package Vantoozz\ProxyScraper\UnitTests\Validators
 */
final class Ipv4RangeValidatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider proxiesDataProvider
     * @param string $string
     * @param bool $expected
     */
    public function is_creates_from_strings(string $string, bool $expected): void
    {
        $validator = new Ipv4RangeValidator;
        try {
            $validator->validate(new Proxy(new Ipv4($string), new Port(8888)));
            $valid = true;
        } catch (ValidationException $e) {
            $valid = false;
        }

        static::assertEquals($valid, $expected);
    }

    /**
     * @return array
     */
    public function proxiesDataProvider(): array
    {
        return [
            ['127.0.0.1', false],
            ['192.168.0.1', false],
            ['10.0.0.1', false],
            ['8.8.8.8', true],
        ];
    }
}
