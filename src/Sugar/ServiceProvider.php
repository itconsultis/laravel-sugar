<?php

namespace ITC\Laravel\Sugar;

use Illuminate\Support\ServiceProvider as ServiceProviderBase;

class ServiceProvider extends ServiceProviderBase
{
    protected $bindings = [
        'register' => [
            [
                'accessor' => Contracts\Serialization\KeyGeneratorInterface::class,
                'resolver' => Serialization\KeyGenerator::class,
            ],
        ],
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerBindings('register');
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->registerBindings('boot');
    }

    /**
     * @param string $phase - "register" or "boot"
     * @return array
     */
    protected function getBindings(string $phase): array
    {
        return $this->bindings[$phase] ?? [];
    }

    /**
     * @param string $phase - "register" or "boot"
     * @return void
     */
    protected function registerBindings($phase)
    {
        foreach ($this->getBindings($phase) as $binding) {
            list($accessor, $resolver, $method ?? 'bind') = $binding;
            $this->app->$method($accessor, $resolver);
        }
    }
}
