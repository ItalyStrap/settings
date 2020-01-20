<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\FileHeader\HeaderFields;
use ItalyStrap\FileHeader\Plugin;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use SplFileInfo;
use SplFileObject;
use UnitTester;

/**
 * @link https://codex.wordpress.org/File_Header
 * Class PluginDataTest
 * @package ItalyStrap\Tests
 */
class HeaderFieldsTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var ObjectProphecy
	 */
	private $file;

	/**
	 * @return SplFileInfo
	 */
	public function getFile(): SplFileObject {
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
		$sut = new HeaderFields( $this->getFile() );
		$this->assertInstanceOf( HeaderFields::class, $sut );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getIntance();
	}

	public function providerPluginFields() {

		$data = [];
		foreach ( Plugin::HEADERS as $key => $value ) {
			$data[ $key ] = [
				$key,
				$value
			];
		}

		return $data;
	}

	/**
	 * @test
	 * @dataProvider providerPluginFields()
	 */
	public function itShouldHasKeyInFields( $key, $value = '' ) {

		$this->file->fread( Argument::type('integer') )->willReturn(
			\file_get_contents( codecept_data_dir( 'fixtures/file-header/plugin.php' ) )
		);

		$sut = $this->getIntance();
		$this->assertArrayHasKey( $key, $sut->fields(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldRenderTextDomain() {

		$this->file->fread( Argument::type('integer') )->willReturn(
			\file_get_contents( codecept_data_dir( 'fixtures/file-header/plugin.php' ) )
		);

		$sut = $this->getIntance();
		$this->assertStringContainsString( 'ItalyStrap', $sut->textDomain(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldRenderTextDomainFromDocBlock() {

		$this->file->fread( Argument::type('integer') )->willReturn(
			\file_get_contents( codecept_data_dir( 'fixtures/file-header/plugin-with-docblock.php' ) )
		);

		$sut = $this->getIntance();
		$this->assertStringContainsString( 'ItalyStrap', $sut->textDomain(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldRenderTextDomainFromStylesCss() {

		$this->file->fread( Argument::type('integer') )->willReturn(
			\file_get_contents( codecept_data_dir( 'fixtures/file-header/styles.css' ) )
		);

		$sut = $this->getIntance();
		$this->assertStringContainsString( 'ItalyStrap', $sut->textDomain(), '' );
	}
}
