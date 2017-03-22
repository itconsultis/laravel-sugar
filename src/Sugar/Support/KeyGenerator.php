<?php

namespace ITC\Laravel\Sugar\Support;

use BadMethodCallException;

trait KeyGenerator
{
    /**
     * Derive a key based on the current class name
     * @param ...string $tokens
     * @return string
     */
    protected static function createKey(...$tokens)
    {
        if (!$tokens) {
            throw new BadMethodCallException('you must supply at least one token');
        }
        return hash('sha1', static::class.implode(':', $tokens));
    }
}

