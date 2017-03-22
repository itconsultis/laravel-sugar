<?php

namespace ITC\Laravel\Sugar\Support;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use ITC\Laravel\Sugar\Contracts\CacheConsumerInterface;

trait CacheConsumer /* implements CacheConsumerInterface */
{
    use KeyGenerator;

    /**
     * The cache key is derived from the return value
     * @param void
     * @return string[]
     * @throws \UnexpectedValueException
     */
    abstract protected function getCacheKeyTokens(): array;

    /**
     * @var \Illuminate\Contracts\Cache\Repository
     */
    private $__cache;

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
     * @satisfies \ITC\Laravel\Sugar\Contracts\CacheConsumerInterface
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
        return app('cache')->driver();
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\CacheConsumerInterface
     * @inheritdoc
     */
    public function createCacheKey(): string
    {
        $tokens = $this->getCacheKeyTokens();
        return static::createKey(...$tokens);
    }

    /**
     * @var array
     * @default null
     */
    private $__cacheTags = null;

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\CacheConsumerInterface
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
        if ($this->__cacheTags === null) {
            $this->__cacheTags = $this->getDefaultCacheTags();
        }
        return $this->__cacheTags;
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
     * @param void
     * @return int
     */
    protected function getDefaultCacheTtl(): int
    {
        return 300; // seconds
    }
}
