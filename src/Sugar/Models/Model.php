<?php

namespace ITC\Laravel\Sugar\Models;

use Illuminate\Database\Eloquent\Model as ModelBase;
use ITC\Laravel\Sugar\Contracts\CacheConsumerInterface;

class Model extends ModelBase implements CacheConsumerInterface
{
    use Behaviors\Caching;

    /**
     * @inheritdoc
     */
    public function getDefaultCacheTtl(): int
    {
        return 300;
    }
}
