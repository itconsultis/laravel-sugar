<?php

namespace ITC\Laravel\Sugar\Serialization;

use ITC\Laravel\Sugar\Tests\TestCase;
use ITC\Laravel\Sugar\Contracts\Serialization\KeyGeneratorInterface;

class KeyGeneratorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->keygen = new KeyGenerator();
    }

    public function interfaces()
    {
        return [
            [KeyGeneratorInterface::class],
        ];
    }

    /**
     * @dataProvider interfaces
     */
    public function test_interface_compliance($interface)
    {
        $this->assertInstanceOf($interface, $this->keygen);
    }

    public function test_namespace_access()
    {
        $default_ns = $this->keygen->getDefaultNamespace();
        $ns = $this->keygen->getNamespace();
        $this->assertSame($default_ns, $ns);

        $ns = 'another-namespace';
        assert($ns != $default_us);
        $this->keygen->setNamespace($ns);
        $this->assertSame($ns, $this->keygen->getNamespace());
    }

    public function test_passes_if_namespace_is_assignable_via_constructor()
    {
        $ns = 'some-namespace';
        $keygen = new KeyGenerator($ns);
        assert($ns != $keygen->getDefaultNamespace());
        $this->assertEquals($ns, $keygen->getNamespace());
    }

    public function test_hashing_algo_access()
    {
        $default_algo = $this->keygen->getDefaultHashingAlgorithm();
        $algo = $this->keygen->getHashingAlgorithm();
        $this->assertEquals($default_algo, $algo);

        $algo = function () {};
        assert($algo != $default_algo);
        $this->keygen->setHashingAlgorithm($algo);
        $this->assertEquals($algo, $this->keygen->getHashingAlgorithm());
    }

    public function createKeyTestInputs()
    {
        return [
            [
                'ns' => 'some-namespace',
                'tokens' => ['foo', 'bar'],
                'expected' => 'some-namespace:54dcbe67d21d5eb39493d46d89ae1f412d3bd6de',
                'algo' => null,
            ],
            [
                'ns' => 'some-namespace',
                'tokens' => ['foo', 'bar'],
                'expected' => 'some-namespace:blah',
                'algo' => function(array $tokens) {return 'blah';},
            ],
        ];
    }

    /**
     * @dataProvider createKeyTestInputs
     */
    public function test_passes_if_createKey_generates_expected_output(string $ns, array $tokens, string $expected, callable $algo=null)
    {
        $algo && $this->keygen->setHashingAlgorithm($algo);
        $actual = $this->keygen->setNamespace($ns)->createKey(...$tokens);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider createKeyTestInputs
     */
    public function test_passes_if_createKey_is_deterministic(string $ns, array $tokens, string $expected)
    {
        $this->keygen->setNamespace($ns);
        $result1 = $this->keygen->createKey(...$tokens);
        $result2 = $this->keygen->createKey(...$tokens);
        $this->assertSame($result1, $result2);
    }
}
