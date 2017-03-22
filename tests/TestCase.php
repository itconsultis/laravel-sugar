<?php

namespace ITC\Laravel\Sugar\Test;

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

    protected function pass()
    {
        $this->assertTrue(true);
    }
}

