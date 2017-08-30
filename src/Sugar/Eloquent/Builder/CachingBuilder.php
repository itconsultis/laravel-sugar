<?php

namespace ITC\Laravel\Sugar\Eloquent\Builder;

use ReflectionClass;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use lluminate\Database\Eloquent\ModelNotFoundException;
use ITC\Laravel\Sugar\Contracts\Cache\ConsumerInterface as CacheConsumerInterface;
use ITC\Laravel\Sugar\Cache\Behaviors\ConsumesCache;
use ITC\Laravel\Sugar\Serialization\Behaviors\GeneratesKeys;

class CachingBuilder extends Builder implements CacheConsumerInterface
{
    use GeneratesKeys;
    use ConsumesCache;

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function setModel(Model $model)
    {
        $this->assertModelRequirements($model);
        return parent::setModel($model);
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function find($id, $columns=['*'])
    {
        $cache = $this->model->getCache();
        $key = $this->createCacheKey('model', [$id], $columns);

        if (!$model = $cache->get($key)) {
            if ($model = parent::find($id, $columns)) {
                $ttl = $model->getCacheTimeout();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $model, $expiry);
            }
        }

        return $model;
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function findMany($ids, $columns=['*'])
    {
        $cache = $this->model->getCache();
        $key = $this->createCacheKey('collection', $ids, $columns);

        if (!$collection = $cache->get($key)) {
            $collection = parent::findMany($ids, $columns);
            if ($collection->isNotEmpty()) {
                $ttl = $this->model->getCacheTimeout();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $collection, $expiry);
            }
        }

        return $collection;
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function findOrNew($id, $columns=['*'])
    {
        $cache = $this->getCache();
        $key = $this->createCacheKey('model', [$id], $columns);

        if (!$model = $model->get($key)) {
            $model = parent::findOrNew($id, $columns);
            if ($model->exists) {
                $ttl = $this->model->getCacheTimeout();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $model, $expiry);
            }
        }

        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function assertModelRequirements(Model $model)
    {
        $kosher = $model instanceof CacheConsumerInterface;
        if (!$kosher) {
            $format = 'model %s must implement interface %s';
            throw new InvalidArgumentException(vsprintf($format, [
                get_class($model),
                CacheConsumerInterface::class,
            ]));
        }
    }

    /**
     * @see \ITC\Laravel\Sugar\Serialization\Behaviors\GeneratesKeys
     * @inheritdoc
     */
    protected function getKeyNamespace(): string
    {
        $refcls = new ReflectionClass($this->model);
        return 'model:'.$refcls->getShortName();
    }

    /**
     * @param string $prefix
     * @param array $ids
     * @param array $columns
     * @return string
     */
    protected function createCacheKey(string $prefix, array $ids, array $columns): string
    {
        sort($ids);
        sort($columns);
        $json = json_encode(['ids'=>$ids, 'columns'=>$columns]);
        return $this->createKey($prefix, $json);
    }
}
