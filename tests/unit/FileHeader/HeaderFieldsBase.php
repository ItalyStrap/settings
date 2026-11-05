<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Prophecy\PhpUnit\ProphecyTrait;
use Codeception\Test\Unit;
use ItalyStrap\FileHeader\HeaderFields;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use SplFileObject;
use UnitTester;

abstract class HeaderFieldsBase extends Unit
{
    use ProphecyTrait;


    /**
     * @var UnitTester
     */
    protected $tester;

    private ?ObjectProphecy $file = null;

    public function getFileObject(): SplFileObject
    {
        return $this->file->reveal();
    }


	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        if (!defined('KB_IN_BYTES')) {
            \define('KB_IN_BYTES', 1024);
        }
        $this->file = $this->prophesize(SplFileObject::class);
    }

    public function getIntance(): HeaderFields
    {
        $sut = new HeaderFields($this->getFileObject(), static::headers());
        $this->assertInstanceOf(HeaderFields::class, $sut);
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable(): void
    {
        $this->getIntance();
    }

    public static function fieldsProvider()
    {

        $data = [];
        foreach (static::headers() as $key => $value) {
            $data[ $key ] = [
                $key,
                static::values()[ $key ]
            ];
        }

        return $data;
    }

    /**
     * @test
     * @dataProvider fieldsProvider
     */
    public function itShouldHasKeyInFields($key, $value = ''): void
    {

        $this->file->fread(Argument::type('integer'))->willReturn(
            \file_get_contents(codecept_data_dir(static::file()))
        );

        $sut = $this->getIntance();
        $this->assertArrayHasKey($key, $sut->fields(), '');
        $this->assertNotEmpty($sut->fields()[ $key ], '');
        $this->assertEquals($value, $sut->fields()[ $key ], '');
        $this->assertStringContainsString($value, $sut->fields()[ $key ], '');
    }

    abstract protected static function values();
    abstract protected static function headers();
    abstract protected static function file();
}
