<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\FileHeader\PluginData;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use SplFileInfo;
use SplFileObject;
use UnitTester;

/**
 * Class PluginDataTest
 * @package ItalyStrap\Tests
 */
class PluginDataTest extends Unit {

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
	public function getFile(): SplFileInfo {
		return $this->file->reveal();
	}


	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		if ( !defined( 'KB_IN_BYTES' ) ) {
			\define( 'KB_IN_BYTES', 1024 );
		}
		$this->file = $this->prophesize( SplFileObject::class );
	}

	public function getIntance(): PluginData {
		$sut = new PluginData( $this->getFile() );
		$this->assertInstanceOf( PluginData::class, $sut );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getIntance();
	}

	/**
	 * @test
	 */
	public function itShouldRenderTextDomain() {

		$this->file->fread( Argument::type('integer') )->willReturn(
			\file_get_contents( codecept_root_dir('index.php') )
		);

		$sut = $this->getIntance();
		$this->assertStringContainsString( 'ItalyStrap', $sut->textDomain(), '' );
	}
}