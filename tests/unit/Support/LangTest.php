<?php

namespace ITC\Laravel\Sugar\Support;

use ITC\Laravel\Sugar\Tests\TestCase;

class LangTest extends TestCase
{
    public function pairsTestInputs()
    {
        return [
            [
                'input' => ['foo'=>1, 'bar'=>2],
                'expected' => [['foo', 1], ['bar', 2]],
            ],
        ];
    }

    /**
     * @dataProvider pairsTestInputs
     */
    public function test_passes_if_pairs_returns_expected_value($input, $expected)
    {
        $actual = Lang::pairs($input);
        $this->assertSame($expected, $actual);
    }
}
