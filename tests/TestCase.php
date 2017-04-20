<?php

namespace ITC\Laravel\Sugar\Tests;

use PHPUnit\Framework\TestCase as TestCaseBase;
use Mockery;

abstract class TestCase extends TestCaseBase
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

