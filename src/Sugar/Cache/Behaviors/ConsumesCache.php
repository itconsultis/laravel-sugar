<?php

namespace ITC\Laravel\Sugar\Cache\Behaviors;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use ITC\Laravel\Sugar\Serialization\Behaviors\GeneratesKeys;
use Carbon\Carbon;

trait ConsumesCache
{
    use GeneratesKeys;

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
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\ConsumerInterface
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
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\ConsumerInterface
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
        return $this->__cacheTags ?? [];
    }

    /**
     * @var int
     */
    private $__defaultCacheTtl = 0;

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\ConsumerInterface
     * @inheritdoc
     */
    public function setDefaultCacheTtl(int $ttl)
    {
        $this->__defaultCacheTtl = $ttl;
        return $this;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Cache\ConsumerInterface
     * @inheritdoc
     */
    public function getDefaultCacheTtl(): int
    {
        return $this->__defaultCacheTtl;
    }

    /**
     * @param integer $ttl - seconds
     * @param \Carbon\Carbon $now - for testing
     * @return \Carbon\Carbon
     */
    public function createCacheExpiry(int $ttl=null, $now=null): Carbon
    {
        $ttl = $ttl ?? $this->getDefaultCacheTtl();
        return ($now ?? Carbon::now())->addSeconds($ttl);
    }
}
