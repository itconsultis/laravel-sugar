<?php

namespace ITC\Laravel\Sugar\Eloquent\Model\Behaviors;

use ReflectionClass;
use ITC\Laravel\Sugar\Eloquent\Builder\CachingBuilder;
use ITC\Laravel\Sugar\Cache\Behaviors\ConsumesCache;
use Illuminate\Support\Str;

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

    protected function getDefaultCacheTags(): array
    {
        $refcls = new ReflectionClass($this);
        $model_slug = Str::slug(Str::plural($refcls->getShortName()));
        return [
            'models',
            "models:$model_slug",
        ];
    }

}
