<?php declare(strict_types = 1);

namespace Vantoozz\ProxyScraper;

/**
 * Class Text
 * @package Vantoozz\ProxyScraper
 */
final class Text
{
    /**
     * @var string
     */
    private $text;

    /**
     * Text constructor.
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @return bool
     */
    public function isHtml(): bool
    {
        return $this->text !== strip_tags($this->text);
    }
}
