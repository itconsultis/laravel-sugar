<?php

namespace ITC\Laravel\Sugar\Test;

use ITC\Laravel\Sugar\Models\Behaviors\Caching as ModelCachingBehavior;
use ITC\Laravel\Sugar\Contracts\CacheConsumerInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CachingBehaviorTestModel extends Model implements CacheConsumerInterface
{
    use ModelCachingBehavior;
    protected $primaryKey = 'id';
    public $id = 123;
    public function getKey()
    {
        return $this->id;
    }
    protected function createCacheKeyTokens(): array
    {
        return ['id', $this->getKey()];
    }
}

class CachingBehaviorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->ttl = 900; // default cache ttl
        $this->cache = mock(CacheRepository::class);
        $this->model = new CachingBehaviorTestModel();
        $this->model->setCache($this->cache);
    }

    public function test_passes_if_createCacheKey_returns_nonempty_String()
    {
        $key = $this->model->createCacheKey();
        $this->assertTrue(is_string($key));
        $this->assertNotEmpty($key);
    }

    public function test_passes_if_createCacheKey_is_deterministic()
    {
        $key1 = $this->model->createCacheKey();
        $key2 = $this->model->createCacheKey();
        $this->assertSame($key1, $key2);
    }

    public function test_passes_if_createCacheKey_return_value_varies_by_model_id()
    {
        $old = ['id'=>$this->model->id, 'key'=>$this->model->createCacheKey()];
        $this->model->id = 8352;
        assert($this->model->id != $old['id']);
        $this->assertNotEquals($this->model->createCacheKey(), $old['key']);
    }

    public function test_passes_if_remember_method_satisfies_cache_expectations()
    {
        $key = $this->model->createCacheKey();
        $expiry = mock(Carbon::class);
        $now = mock(Carbon::class)->makePartial();
        $now->expects()->addSeconds()->withArgs([$this->ttl])->andReturns($expiry);
        $this->assertTrue(true);
        $this->cache->expects()->put()->once()->withArgs([$key, $this->model, $expiry]);
        $this->model->remember($this->ttl, $now);
        return $this->pass();
    }

    public function test_passes_if_recall_method_satisfies_cache_expectations()
    {
        $key = $this->model->createCacheKey();
        $proto = mock(CacheConsumerInterface::class);
        $proto->primaryKey = 'id';
        $proto->id = $this->model->id;
        $proto->expects()->createCacheKey()->andReturns($key);
        $this->cache->expects()->get()->once()->withArgs([$key])->andReturns($this->model);
        CachingBehaviorTestModel::recall($key, $this->cache, $proto);
        return $this->pass();
    }

    public function test_passes_if_forget_method_satisfies_cache_expectations()
    {
        $key = $this->model->createCacheKey();
        $this->cache->expects()->forget()->once()->withArgs([$key]);
        $this->model->forget();
        return $this->pass();
    }

    public function emptyValues()
    {
        return [
            [null],
            [''],
            [0],
            [false],
        ];
    }

    /**
     * @dataProvider emptyValues
     * @expectedException \UnexpectedValueException
     */
    public function test_passes_if_createCacheKey_raises_UnexpectedValueException($empty)
    {
        $this->model->id = $empty;
        $this->model->createCacheKey();
    }
}
