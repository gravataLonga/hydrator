<?php declare(strict_types=1);

namespace Tests;

use Gravatalonga\Hydrator\HydrableInterface;
use Gravatalonga\Hydrator\HydrateTrait;
use Gravatalonga\Hydrator\HydratorException;
use PHPUnit\Framework\TestCase;

class TraitHydratorTest extends TestCase
{
    /** @test */
    public function populate_class()
    {
        $sut = Sut::hydrate(['publicPropertyId' => 1]);

        $this->assertEquals(1, $sut->publicPropertyId);
    }

    /** @test */
    public function cant_populate_private_class()
    {
        $this->expectException(HydratorException::class);
        $this->expectExceptionMessage('property privatePropertyId is private/protected and can not be hydrable');

        Sut::hydrate(['privatePropertyId' => 1]);
    }

    /** @test */
    public function cant_populate_protected_class()
    {
        $this->expectException(HydratorException::class);
        $this->expectExceptionMessage('property protectedPropertyId is private/protected and can not be hydrable');

        Sut::hydrate(['protectedPropertyId' => 1]);
    }

    /** @test */
    public function typed_hint_int()
    {
        $sut = Sut::hydrate(['intProperty' => 1]);

        $this->assertEquals(1, $sut->intProperty);
    }

    /**
     * @test
     * @dataProvider dataProviderFormatValueToInt
     */
    public function typed_hint_int_from_string($data, $expected)
    {
        $sut = Sut::hydrate(['intProperty' => $data]);

        $this->assertEquals($expected, $sut->intProperty);
    }

    /**
     * @test
     * @dataProvider dataProviderFormatValueToStr
     */
    public function typed_hint_string_from_int($data, $expected)
    {
        $sut = Sut::hydrate(['strProperty' => $data]);

        $this->assertEquals($expected, $sut->strProperty);
    }

    /**
     * @test
     * @dataProvider dataProviderFormatValueToBoolean
     */
    public function typed_hint_boolean_from_data($data, $expected)
    {
        $sut = Sut::hydrate(['booleanProperty' => $data]);

        $this->assertEquals($expected, $sut->booleanProperty);
    }

    /**
     * @test
     * @dataProvider dataProviderFormatValueToFloat
     */
    public function typed_hint_float($data, $expected)
    {
        $sut = Sut::hydrate(['floatProperty' => $data]);

        $this->assertEquals($expected, $sut->floatProperty);
    }

    /** @test */
    public function if_property_not_exists_do_nothing()
    {
        $sut = Sut::hydrate(['lool' => '1']);

        $this->assertInstanceOf(Sut::class, $sut);
        $this->assertFalse(property_exists($sut, 'lool'));
    }

    /** @test */
    public function hydrate_non_type_hint_property()
    {
        $sut = Sut::hydrate(['propertyMixed' => 1]);
        $this->assertEquals(1, $sut->propertyMixed);
    }

    /** @test */
    public function hydrate_custom_datatype()
    {
        $dt = new \DateTime();
        $sut = Sut::hydrate(['propertyWithDataType' => $dt]);
        $this->assertEquals($dt, $sut->propertyWithDataType);
    }

    /** @test */
    public function it_call_custom_format_value()
    {
        $sut = Sut::hydrate(['propertyCustomFormat' => 'Yes']);
        $this->assertEquals('Hello', $sut->propertyCustomFormat);
    }

    public function dataProviderFormatValueToBoolean()
    {
        return [
            [0, false],
            ['0', false],
            ['', false],
            [null, false],
            [false, false],
            ['1', true],
            [1, true],
        ];
    }

    public function dataProviderFormatValueToFloat()
    {
        return [
            ['0', 0.0],
            ['0.1', 0.1],
            [2, 2.0],
            [null, 0],
            ['', 0],
            ['2.5', 2.5],
        ];
    }

    public function dataProviderFormatValueToInt()
    {
        return [
            ['0', 0],
            ['', 0],
            ['1', 1],
            ['0.1', 0],
            [100, 100],
        ];
    }

    public function dataProviderFormatValueToStr()
    {
        return [
            [0, '0'],
            ['1', '1'],
            ['', ''],
            [null, null],
        ];
    }
}

class Sut implements HydrableInterface
{
    use HydrateTrait;

    public $publicPropertyId;

    private $privatePropertyId;

    protected $protectedPropertyId;

    public int $intProperty;

    public string $strProperty;

    public bool $booleanProperty;

    public float $floatProperty;

    public $propertyMixed;

    public \DateTime $propertyWithDataType;

    public $propertyCustomFormat;

    public function formatPropertyCustomFormat($value)
    {
        return 'Hello';
    }
}
