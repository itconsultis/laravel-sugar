<?php

namespace ITC\Laravel\Sugar\Contracts;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

interface CacheConsumerInterface
{
    /**
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @return static
     */
    public function setCache(CacheRepository $cache);

    /**
     * @param void
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getCache(): CacheRepository;

    /**
     * @param void
     * @return string
     */
    public function createCacheKey(): string;

    /**
     * @param string[] $tags
     * @return static
     */
    public function setCacheTags(array $tags);

    /**
     * @param void
     * @return string[]
     */
    public function getCacheTags(): array;
}
