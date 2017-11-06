<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper;

/**
 * Class Metric
 * @package Vantoozz\ProxyScraper
 */
final class Metric
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var
     */
    private $value;

    /**
     * Metric constructor.
     * @param string $name
     * @param mixed $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name . ': ' . (string)$this->value;
    }
}
