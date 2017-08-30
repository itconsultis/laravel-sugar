<?php

namespace ITC\Laravel\Sugar\Eloquent\Builder;

use Closure;
use ReflectionClass;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use lluminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use ITC\Laravel\Sugar\Contracts\Cache\CacheConsumerInterface;
use ITC\Laravel\Sugar\Cache\Behaviors\ConsumesCache;
use ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface;
use SuperClosure\Serializer as ClosureSerializer;

class CachingBuilder extends Builder implements CacheConsumerInterface
{
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
                $ttl = $this->model->getCacheTimeout();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $model, $expiry);
            }
        }
        else {
            app('log')->debug('HIT', [
                'context' => __METHOD__,
                'model' => get_class($this->model),
                'key' => $key,
            ]);
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
        else {
            app('log')->debug('HIT', [
                'context' => __METHOD__,
                'model' => get_class($this->model),
                'key' => $key,
            ]);
        }

        return $collection;
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function findOrNew($id, $columns=['*'])
    {
        $cache = $this->model->getCache();
        $key = $this->createCacheKey('model', [$id], $columns);

        if (!$model = $model->get($key)) {
            $model = parent::findOrNew($id, $columns);
            if ($model->exists) {
                $ttl = $this->model->getCacheTimeout();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $model, $expiry);
            }
        }
        else {
            app('log')->debug('HIT', [
                'context' => __METHOD__,
                'model' => get_class($this->model),
                'key' => $key,
            ]);
        }

        return $model;
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    public function get($cols=['*'])
    {
        $cache = $this->model->getCache();
        $key = $this->createCacheKey('collection', [], $cols);

        if (!$collection = $cache->get($key)) {
            $collection = parent::get($cols);
            if ($collection->isNotEmpty()) {
                $ttl = $this->model->getCacheTimeout();
                $expiry = $this->createCacheExpiry($ttl);
                $cache->put($key, $collection, $expiry);
            }
        }
        else {
            app('log')->debug('HIT', [
                'context' => __METHOD__,
                'model' => get_class($this->model),
                'key' => $key,
            ]);
        }

        return $collection;
    }

    /**
     * @overrides \Illuminate\Database\Eloquent\Builder
     * @inheritdoc
     */
    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param  array  $models
     * @param  string  $name
     * @param  \Closure  $constraints
     * @return array
     */
    protected function eagerLoadRelation(array $models, $name, Closure $constraints)
    {
        $cache = $this->getCache();
        $key = $this->createRelationCacheKey($models, $name, $constraints);

        if (!$relation = $cache->get($key)) {
            $relation = parent::eagerLoadRelation($models, $name, $constraints);
            $ttl = $this->model->getCacheTimeout();
            $expiry = $this->createCacheExpiry($ttl);
            $cache->put($key, $relation, $expiry);
        }
        else {
            app('log')->debug('HIT', [
                'context' => __METHOD__,
                'model' => get_class($this->model),
                'key' => $key,
            ]);
        }

        return $relation;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function assertModelRequirements(Model $model)
    {
        $kosher = $model instanceof CacheConsumerInterface
                  && method_exists($model, 'getTable');

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
    protected function getCacheKeyNamespace(): string
    {
        return 'model:'.$this->model->getTable();
    }

    /**
     * Return a concrete KeyGeneratorInterface
     * @param void
     * @return \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     */
    protected function getCacheKeyGenerator(): KeyGeneratorInterface
    {
        $keygen = app(KeyGeneratorInterface::class);
        $keygen->setNamespace($this->getCacheKeyNamespace());
        return $keygen;
    }

    /**
     * Cache key hashing function for model and collections queries
     * @param string $prefix
     * @param array $ids
     * @param array $columns
     * @return string
     */
    protected function createCacheKey(string $prefix, array $ids, array $columns): string
    {
        sort($ids);
        sort($columns);
        $precursor = json_encode(['ids'=>$ids, 'columns'=>$columns]);
        return $this->getCacheKeyGenerator()->createKey($prefix, $precursor);
    }

    /**
     * Cache key hashing function for model relation queries
     * @param array $models
     * @param string $name - relation name
     * @param Closure $contraints
     * @return string
     */
    protected function createRelationCacheKey(array $models, string $name, Closure $constraints): string
    {
        $model_ids = array_map(function($model) {return $model->getKey();}, $models);
        $sconstraints = (new ClosureSerializer)->serialize($constraints);
        $precursor = implode(',', $model_ids).':'.$sconstraints;
        return $this->getCacheKeyGenerator()->createKey('related', $name, $precursor);
    }
}
