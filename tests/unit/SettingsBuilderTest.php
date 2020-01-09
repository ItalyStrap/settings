<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Settings\SettingsBuilder;

/**
 * Class SettingsBuilderTest
 * @package ItalyStrap\Tests
 */
class SettingsBuilderTest extends \Codeception\Test\Unit {

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
		$sut = new SettingsBuilder();
		$this->assertInstanceOf( SettingsBuilder::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
//	public function itShouldBeInstantiable() {
//		$sut = $this->getInstance();
//	}
}
