<?php

namespace ITC\Laravel\Sugar\Serialization;

use InvalidArgumentException;
use ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface;

class KeyGenerator implements KeyGeneratorInterface
{
    /**
     * @var string
     * @default null
     */
    private $ns = null;

    /**
     * @param string $ns
     */
    public function __construct(string $ns=null)
    {
        $ns && $this->setNamespace($ns);
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     * @inheritdoc
     */
    public function setNamespace(string $ns)
    {
        $this->ns = $ns;
        return $this;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     * @inheritdoc
     */
    public function getNamespace(): string
    {
        return $this->ns ?? $this->getDefaultNamespace();
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     * @inheritdoc
     */
    public function getDefaultNamespace(): string
    {
        return 'default';
    }

    /**
     * @param string[] $tokens
     * @return array
     */
    protected function mapKeyTokens(array $tokens): array
    {
        return $tokens;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     * @inheritdoc
     */
    public function createKey(...$tokens): string
    {
        $algo = $this->getHashingAlgorithm();
        $tokens = $this->mapKeyTokens($tokens);
        return $this->ns.':'.$algo($tokens);
    }

    /**
     * @var callable
     * @default null
     */
    private $__keyHashingAlgorithm = null;

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     * @inheritdoc
     */
    public function setHashingAlgorithm(callable $algo)
    {
        $this->__keyHashingAlgorithm = $algo;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     * @inheritdoc
     */
    public function getHashingAlgorithm(): callable
    {
        if (!$this->__keyHashingAlgorithm) {
            $default = $this->getDefaultHashingAlgorithm();
            $this->__keyHashingAlgorithm = $default;
        }
        return $this->__keyHashingAlgorithm;
    }

    /**
     * @satisfies \ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface
     * @inheritdoc
     */
    public function getDefaultHashingAlgorithm(): callable
    {
        return function (array $tokens) {
            return hash('sha1', implode(':', $tokens));
        };
    }
}

