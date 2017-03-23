<?php

namespace ITC\Laravel\Sugar\Serialization\Behaviors;

use ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface;
use ITC\Laravel\Sugar\Serialization\KeyGenerator;
use ReflectionClass;

trait GeneratesKeys
{
    /**
     * @param void
     * @return string
     */
    public function getKeyNamespace(): string
    {
        $refcls = new ReflectionClass($this);
        return $refcls->getShortName();
    }

    /**
     * @param string $tokens,...
     * @return string
     */
    public function createKey(...$tokens): string
    {
        return $this->getKeyGenerator()->createKey(...$tokens);
    }

    /**
     * @param void
     * @return \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     */
    public function getKeyGenerator(): KeyGeneratorInterface
    {
        $keygen = new KeyGenerator();
        $keygen->setNamespace($this->getKeyNamespace());
        return $keygen;
    }
}
