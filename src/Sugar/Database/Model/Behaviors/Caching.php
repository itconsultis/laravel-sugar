<?php

namespace ITC\Laravel\Sugar\Database\Model\Behaviors;

use ITC\Laravel\Sugar\Database\Builder\CachingBuilder;
use ITC\Laravel\Sugar\Cache\Behaviors\ConsumesCache;

trait Caching
{
    use ConsumesCache;

    /**
     * @overrides \Illuminate\Database\Eloquent\Model
     * @inheritdoc
     */
    public function newEloquentBuilder($query)
    {
        return new CachingBuilder($query);
    }
}
