<?php declare(strict_types=1);

namespace Vantoozz\ProxyScraper\SystemTests\ProxiesMiner;

/**
 * Class Cached
 * @package Vantoozz\ProxyScraper\SystemTests\ProxiesMiner
 */
final class Cached implements ProxiesMinerInterface
{
    /**
     * @var ProxiesMinerInterface
     */
    private $miner;

    /**
     * @var string
     */
    private $filename;

    /**
     * Cached constructor.
     * @param ProxiesMinerInterface $miner
     * @param string $filename
     */
    public function __construct(ProxiesMinerInterface $miner, string $filename)
    {
        $this->miner = $miner;
        $this->filename = $filename;
    }

    /**
     * @return array
     */
    public function getProxies(): array
    {
        if (file_exists($this->filename)) {
            return json_decode(file_get_contents($this->filename), true);
        }
        $proxies = $this->miner->getProxies();
        file_put_contents($this->filename, json_encode($proxies));

        return $proxies;
    }
}
