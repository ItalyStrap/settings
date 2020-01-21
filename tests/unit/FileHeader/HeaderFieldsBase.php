<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\FileHeader\HeaderFields;
use ItalyStrap\FileHeader\Plugin;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use SplFileObject;
use UnitTester;

abstract class HeaderFieldsBase extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var ObjectProphecy
	 */
	private $file;

	/**
	 * @return SplFileObject
	 */
	public function getFileObject(): SplFileObject {
		return $this->file->reveal();
	}


	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		if ( !defined( 'KB_IN_BYTES' ) ) {
			\define( 'KB_IN_BYTES', 1024 );
		}
		$this->file = $this->prophesize( SplFileObject::class );
	}

	public function getIntance(): HeaderFields {
		$sut = new HeaderFields( $this->getFileObject(), $this->headers() );
		$this->assertInstanceOf( HeaderFields::class, $sut );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getIntance();
	}

	public function fieldsProvider() {

		$data = [];
		foreach ( $this->headers() as $key => $value ) {
			$data[ $key ] = [
				$key,
				$this->values()[ $key ]
			];
		}

		return $data;
	}

	/**
	 * @test
	 * @dataProvider fieldsProvider()
	 */
	public function itShouldHasKeyInFields( $key, $value = '' ) {

		$this->file->fread( Argument::type('integer') )->willReturn(
			\file_get_contents( codecept_data_dir( $this->file() ) )
		);

		$sut = $this->getIntance();
		$this->assertArrayHasKey( $key, $sut->fields(), '' );
		$this->assertNotEmpty( $sut->fields()[ $key ], '' );
		$this->assertEquals( $value, $sut->fields()[ $key ], '' );
		$this->assertStringContainsString( $value, $sut->fields()[ $key ], '' );
	}

	abstract protected function values();
	abstract protected function headers();
	abstract protected function file();
}
