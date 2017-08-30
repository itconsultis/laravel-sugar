<?php

namespace ITC\Laravel\Sugar;

use Illuminate\Support\ServiceProvider as ServiceProviderBase;

class SugarServiceProvider extends ServiceProviderBase
{
    protected function bindings()
    {
        return [
            'register' => [
                [
                    'accessor' => Contracts\Serialization\KeyGeneratorInterface::class,
                    'resolver' => Serialization\KeyGenerator::class,
                ],
            ],
            'boot' => [

            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->moment('register');
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->moment('boot');
    }

    /**
     * @param string $phase - "register" or "boot"
     * @return void
     */
    protected function moment($phase)
    {
        foreach ($this->bindings()[$phase] ?? [] as $binding) {
            $accessor = $binding['accessor'];
            $resolver = $binding['resolver'];
            $method = $binding['method'] ?? 'bind';
            $this->app->$method($accessor, $resolver);
        }
    }
}
