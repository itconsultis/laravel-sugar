<?php

namespace ITC\Laravel\Sugar\Cache\Behaviors;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Carbon\Carbon;

trait ConsumesCache
{

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $__cache = null;

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\CacheConsumerInterface
     * @inheritdoc
     */
    public function setCache(CacheRepository $cache)
    {
        $this->__cache = $cache;
        return $this;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\CacheConsumerInterface
     * @inheritdoc
     */
    public function getCache(): CacheRepository
    {
        if (!$this->__cache) {
            $this->__cache = $this->getDefaultCache();
        }
        $tags = $this->getCacheTags();
        return $tags ? $this->__cache->tags($tags) : $this->__cache;
    }

    /**
     * @param void
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getDefaultCache(): CacheRepository
    {
        return app(CacheRepository::class);
    }

    /**
     * @var array
     * @default null
     */
    private $__cacheTags = null;

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\CacheConsumerInterface
     * @inheritdoc
     */
    public function setCacheTags(array $tags)
    {
        $this->__cacheTags = $tags;
        return $this;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\CacheConsumerInterface
     * @inheritdoc
     */
    public function getCacheTags(): array
    {
        return $this->__cacheTags ?? $this->getDefaultCacheTags();
    }

    /**
     * @param void
     * @return array
     */
    protected function getDefaultCacheTags(): array
    {
        return [];
    }

    /**
     * @var int
     */
    private $__cacheTimeout = null;

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\CacheConsumerInterface
     * @inheritdoc
     */
    public function setCacheTimeout(int $ttl)
    {
        $this->__cacheTimeout = $ttl;
        return $this;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\CacheConsumerInterface
     * @inheritdoc
     */
    public function getCacheTimeout(): int
    {
        return $this->__cacheTimeout ?? $this->getDefaultCacheTimeout();
    }

    /**
     * @param void
     * @return int
     */
    protected function getDefaultCacheTimeout(): int
    {
        return 60;
    }

    /**
     * @param integer $ttl - seconds
     * @param \Carbon\Carbon $now - facilitates testing
     * @return \Carbon\Carbon
     */
    public function createCacheExpiry(int $ttl=null, $now=null): Carbon
    {
        $ttl = $ttl ?? $this->getDefaultCacheTimeout();
        return ($now ?? Carbon::now())->addSeconds($ttl);
    }
}
