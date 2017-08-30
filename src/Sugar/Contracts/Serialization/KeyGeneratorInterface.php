<?php

namespace ITC\Laravel\Sugar\Contracts\Serialization;

interface KeyGeneratorInterface
{
    /**
     * @param string $tokens,...
     * @return string
     * @throws \InvalidArgumentException
     */
    public function createKey(...$tokens): string;

    /**
     * @param string $ns
     * @return static
     */
    public function setNamespace(string $ns);

    /**
     * @param void
     * @return string
     */
    public function getNamespace(): string;

    /**
     * @param void
     * @return string
     */
    public function getDefaultNamespace(): string;

    /**
     * @param callable $algo
     * @return static
     */
    public function setHashingAlgorithm(callable $algo);

    /**
     * @param void
     * @return callable
     */
    public function getHashingAlgorithm(): callable;

    /**
     * @param void
     * @return callable
     */
    public function getDefaultHashingAlgorithm(): callable;
}
