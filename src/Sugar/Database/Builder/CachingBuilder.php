<?php

namespace ITC\Laravel\Sugar\Database\Builder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use lluminate\Database\Eloquent\ModelNotFoundException;
use ITC\Laravel\Sugar\Contracts\Cache\ConsumerInterface as CacheConsumerInterface;
use ITC\Laravel\Sugar\Cache\Behaviors\ConsumesCache;
use ITC\Laravel\Sugar\Serialization\Behaviors\GeneratesKeys;
use InvalidArgumentException;

class CachingBuilder extends Builder implements CacheConsumerInterface
{
    use ConsumesCache;
    use GeneratesKeys;

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function setModel(Model $model)
    {
        $this->checkModelRequirements($model);
        return parent::setModel($model);
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function find($id, $columns=['*'])
    {
        if (is_array($id)) {
            return $this->findMany($id, $columns);
        }
        $cache = $this->getCache();
        $key = $this->createCacheKey('model', [$id], $columns);
        if (!$model = $cache->get($key)) {
            if ($model = parent::find($id, $columns)) {
                $ttl = $this->model->getDefaultCacheTtl();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $model, $expiry);
            }
        }
        else {
            app('log')->debug('cache hit on key '.$key);
        }

        return $model;
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function findMany($ids, $columns=['*'])
    {
        $cache = $this->getCache();
        $key = $this->createCacheKey('collection', $ids, $columns);

        if (!$collection = $cache->get($key)) {
            $collection = parent::findMany($ids, $columns);
            if (!$collection->isEmpty()) {
                $ttl = $this->model->getDefaultCacheTtl();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $collection, $expiry);
            }
        }
        else {
            app('log')->debug('cache hit on key '.$key);
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
                $ttl = $this->model->getDefaultCacheTtl();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $model, $expiry);
            }
        }
        else {
            app('log')->debug('cache hit on key '.$key);
        }

        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function checkModelRequirements(Model $model)
    {
        return;
        // no-op
        //$kosher = $model instanceof CacheConsumerInterface;
        //if (!$kosher) {
        //    $format = 'model %s must implement interface %s';
        //    throw new InvalidArgumentException(vsprintf($format, [
        //        get_class($model),
        //        CacheConsumerInterface::class,
        //    ]));
        //}
    }

    /**
     * @overrides \ITC\Laravel\Sugar\Serialization\Behaviors\GeneratesKeys
     * @inheritdoc
     */
    protected function getKeyNamespace(): string
    {
        $refcls = new ReflectionClass($this->model);
        return 'Model:'.$refcls->getShortName();
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
