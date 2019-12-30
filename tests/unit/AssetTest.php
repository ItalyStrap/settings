<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

class AssetTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance() {
		$config = $this->make( \ItalyStrap\Config\Config::class );
		$sut = new \ItalyStrap\Settings\AssetLoader( $config );
		$this->assertInstanceOf( \ItalyStrap\Settings\AssetLoader::class, $sut, '' );

		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$this->getInstance();
	}
}
