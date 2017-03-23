<?php

namespace ITC\Laravel\Sugar\Contracts\Cache;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

interface ConsumerInterface
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
     * @param string $tokens,... cache key tokens
     * @return string
     * @throws \InvalidArgumentException
     */
    public function createCacheKey(...$tokens): string;

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

    /**
     * @param void
     * @return int
     */
    public function getDefaultCacheTtl(): int;

    /**
     * @param int $ttl
     * @return static
     */
    public function setDefaultCacheTtl(int $ttl);
}
