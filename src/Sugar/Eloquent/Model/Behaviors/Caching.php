<?php

namespace ITC\Laravel\Sugar\Eloquent\Model\Behaviors;

use ITC\Laravel\Sugar\Eloquent\Builder\CachingBuilder;
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
