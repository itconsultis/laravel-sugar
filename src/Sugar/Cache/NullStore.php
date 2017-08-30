<?php

use Illuminate\Contracts\Cache\Store as CacheStoreInterface;

class NullStore implements CacheStoreInterface
{
    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function get($key)
    {
        return null;
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function many(array $keys)
    {
        return null;
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function put($key, $value, $minutes)
    {
        // no-op
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function putMany(array $values, $minutes)
    {
        // no-op
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function increment($key, $value = 1)
    {
        return false;
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function decrement($key, $value = 1)
    {
        return false;
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function forever($key, $value)
    {
        // no-op
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function forget($key)
    {
        return true;
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function flush()
    {
        return true;
    }

    /**
     * @satisfies \Illuminate\Contracts\Cache\Store
     * @inheritdoc
     */
    public function getPrefix()
    {
        return __CLASS__;
    }
}
