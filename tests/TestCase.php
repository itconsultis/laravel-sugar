<?php

namespace ITC\Laravel\Sugar\Tests;

use Mockery;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        Mockery::close();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @param void
     * @return void
     */
    protected function pass()
    {
        $this->assertTrue(true);
    }
}

