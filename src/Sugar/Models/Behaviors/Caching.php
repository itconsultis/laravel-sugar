<?php

namespace ITC\Laravel\Sugar\Models\Behaviors;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use ITC\Laravel\Sugar\Contracts\CacheConsumerInterface;
use ITC\Laravel\Sugar\Support\CacheConsumer;
use Carbon\Carbon;
use UnexpectedValueException;

/**
 * Caching trait for Eloquent models
 *
 * ```php
 * use App\Models\Car;
 *
 * $car1 = Car::find(123);
 *
 * // cache the model
 * $car1->remember();
 *
 * // recall the cached model
 * $car2 = Car::recall(123);
 *
 * assert($car1->getAttributes() == $car2->getAttributes());
 *
 * // uncache the model
 * $car2->forget();
 * ```
 */
trait Caching
{
    use CacheConsumer;

    /**
     * Recall a cached model instance
     * @param mixed $id
     * @param \Illuminate\Contracts\Cache\Repository $cache
     * @param \ITC\Laravel\Sugar\Contracts\CacheConsumerInterface $proto - model instance; facilities unit tests
     * @return static|null
     */
    public static function recall(
        $id,
        CacheRepository $cache=null,
        CacheConsumerInterface $model=null
    )
    {
        $model = $model ?? new static();
        $pk = $model->primaryKey;
        $model->$pk = $id;
        $key = $model->createCacheKey();
        $cache = $cache ?? $model->getCache();
        return $cache->get($key);
    }

    /**
     * Cache the model instance
     * @param int $ttl - cache TTL in seconds
     * @param \Carbon\Carbon $now - this parameter facilitates unit tests
     * @return static
     */
    public function remember(int $ttl=null, Carbon $now=null): Model
    {
        $ttl = $ttl ?? $this->getDefaultCacheTtl();
        $now = $now ?? Carbon::now();
        $expiry = $now->addSeconds($ttl);
        $key = $this->createCacheKey();
        $cache = $this->getCache();
        $cache->put($key, $this, $expiry);
        return $this;
    }

    /**
     * Uncache the model instance
     * @param void
     * @return static
     */
    public function forget(): Model
    {
        $tags = $this->getCacheTags();
        $key = $this->createCacheKey();
        $cache = $this->getCache();
        $cache->forget($key);
        return $this;
    }

    /**
     * @overrides \ITC\Laravel\Sugar\Support\CacheConsumer::getCacheKeyTokens
     * @inheritdoc
     */
    protected function getCacheKeyTokens(): array
    {
        $id = $this->getKey();
        if (!$id) {
            throw new UnexpectedValueException('expected model to have an id');
        }
        return ['id', $this->getKey()];
    }
}

