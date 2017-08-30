<?php

namespace ITC\Laravel\Sugar\Serialization\Behaviors;

use ITC\Laravel\Sugar\Tests\TestCase;
use ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface;
use ITC\Laravel\Sugar\Serialization\KeyGenerator;
use Exception;

class GeneratesKeysConsumer
{
    use GeneratesKeys;
}

class GeneratesKeysTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->consumer = new GeneratesKeysConsumer();
    }

    public function test_passes_if_createKey_does_not_raise_exception()
    {
        try {
            $this->consumer->createKey('foo', 'bar');
            return $this->pass();
        }
        catch (Exception $e) {
            return $this->fail('caught '.get_class($e));
        }
    }
}
