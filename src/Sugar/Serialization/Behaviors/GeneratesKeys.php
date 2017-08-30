<?php

namespace ITC\Laravel\Sugar\Serialization\Behaviors;

use ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface;
use ITC\Laravel\Sugar\Serialization\KeyGenerator;
use ReflectionClass;

trait GeneratesKeys
{
    /**
     * @param string $tokens,...
     * @return string
     */
    public function createKey(...$tokens): string
    {
        $keygen = $this->getKeyGenerator();
        $keygen->setNamespace($this->getKeyNamespace());
        return $keygen->createKey(...$tokens);
    }

    /**
     * @param void
     * @return string
     */
    protected function getKeyNamespace(): string
    {
        $refcls = new ReflectionClass($this);
        return $refcls->getShortName();
    }


    /**
     * @param void
     * @return \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     */
    protected function getKeyGenerator(): KeyGeneratorInterface
    {
        return app(KeyGeneratorInterface::class);
    }
}
